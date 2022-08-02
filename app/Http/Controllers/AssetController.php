<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Asset;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Exports\AssetsReport;
use App\Http\Requests\AssignAsset;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\AssetsReportQuery;
use App\Http\Requests\StoreAsset;
use App\Http\Requests\UpdateAsset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetController extends Controller
{
    public function index(): RedirectResponse|View
    {
        $hotels = Hotel::whereHas('owner', function (Builder $query) {
            $query->where('id', id_parent());
        })->with([
            'assets' => function ($query) {
                $query->select(fields_get('assets'));
            }
        ])->get(fields_get('hotels'));

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('hotels.index');
        }

        $hotels = $this->prepareData($hotels);

        return view('app.assets.index', compact('hotels'));
    }

    private function prepareData(Collection $hotels): Collection
    {
        $hotels = $hotels->map(function ($hotel)
        {
            $hotel->user_id = id_encode($hotel->user_id);
            $hotel->main_hotel = empty($hotel->main_hotel) ? null : id_encode($hotel->main_hotel);
            $hotel->assets = $hotel->assets->map(function ($asset)
            {
                $asset->hotel_id = id_encode($asset->hotel_id);
                $asset->user_id = id_encode($asset->user_id);
                $asset->room_id = $asset->room_id ? id_encode($asset->room_id) : null;

                return $asset;
            });

            return $hotel;
        });

        return $hotels;
    }

    public function create(): RedirectResponse|View
    {
        $hotels = Hotel::whereHas('owner', function (Builder $query) {
            $query->where('id', id_parent());
        })->where('status', true)
        ->with([
            'rooms' => function ($query) {
                $query->select(fields_get('rooms'));
            }
        ])->get(fields_get('hotels'));

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('hotels.index');
        }

        $rooms = $hotels->sum(function ($hotel) {
            return $hotel->rooms->count();
        });

        if($rooms === 0) {
            flash(trans('rooms.no.created'))->info();

            return redirect()->route('assets.index');
        }

        return view('app.assets.create', compact('hotels'));
    }

    public function store(StoreAsset $request): RedirectResponse
    {
        $asset = new Asset();
        $asset->number = $request->input('number');
        $asset->description = $request->input('description');
        $asset->brand = $request->input('brand');
        $asset->model = $request->input('model');
        $asset->serial_number = $request->input('serial_number');
        $asset->price = (float) $request->input('price');
        $asset->location = $request->input('location');
        $asset->user()->associate(id_parent());
        $asset->hotel()->associate(id_decode($request->hotel));

        if ($request->filled('room')) {
            $asset->room()->associate(id_decode($request->input('room')));
        }

        $asset->save();

        flash(trans('common.createdSuccessfully'))->success();

        return redirect()->route('assets.show', [
            'id' => $asset->hash,
        ]);
    }

    public function show(string $id): View
    {
        $asset = Asset::whereOwner()
            ->where('id', id_decode($id))
            ->firstOrFail(fields_get('assets'));

        $asset->load([
            'room' => function ($query) {
                $query->select('id', 'number', 'description');
            },
            'hotel' => function ($query) {
                $query->select('id', 'business_name');
            },
            'maintenances' => function ($query) {
                $query->select(fields_get('maintenances'))
                    ->orderBy('date', 'DESC');
            }
        ]);

        return view('app.assets.show', compact('asset'));
    }

    public function edit(string $id): View
    {
        $asset = Asset::whereOwner()
            ->where('id', id_decode($id))
            ->firstOrFail(fields_get('assets'));

        $asset->load([
            'room' => function ($query) {
                $query->select('id', 'number');
            },
            'hotel' => function ($query) {
                $query->select('id', 'business_name');
            },
            'hotel.rooms' => function ($query) {
                $query->select('id', 'number', 'hotel_id');
            },
        ]);

        $hotels = Hotel::where('user_id', id_parent())
            ->where('id', '!=', $asset->hotel->id)
            ->where('status', true)
            ->get(fields_get('hotels'));

        return view('app.assets.edit', compact('asset', 'hotels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAsset $request, $id)
    {
        $asset = Asset::whereOwner()
            ->where('id', id_decode($id))
            ->where('hotel_id', id_decode($request->hotel))
            ->firstOrFail(fields_get('assets'));

        $asset->description = $request->input('description');
        $asset->brand = $request->input('brand');
        $asset->model = $request->input('model');
        $asset->serial_number = $request->input('serial_number');
        $asset->price = (float) $request->input('price');
        $asset->location = $request->input('location');
        $asset->hotel()->associate(id_decode($request->hotel));

        if (empty($request->input('room'))) {
            $asset->room()->dissociate();
        } else {
            $room = Room::where('id', id_decode($request->room))
                ->where('hotel_id', id_decode($request->hotel))
                ->where('user_id', id_parent())
                ->firstOrFail(['id']);

            $asset->room()->associate($room);
        }

        $asset->save();

        flash(trans('common.updatedSuccessfully'))->success();

        return redirect()->route('assets.show', [
            'id' => $asset->hash,
        ]);
    }

    public function destroy(string $id): RedirectResponse
    {
        $asset = Asset::whereOwner()
            ->where('id', id_decode($id))
            ->firstOrFail(['id']);

        $asset->delete();

        flash(trans('common.deletedSuccessfully'))->success();

        return redirect()->route('assets.index');
    }

    public function search(Request $request): JsonResponse
    {
        $query = clean_param($request->get('query', null));

        /** @var \Illuminate\Support\Collection $assets */
        $assets = Asset::where('hotel_id', id_decode($request->hotel))
            ->where('user_id', id_parent())
            ->whereLike(['number', 'description', 'brand', 'model', 'serial_number', 'location'], $query)
            ->get(fields_get('assets'));

        $assets->transform(function ($asset) {
            $asset->hotel_id = id_encode($asset->hotel_id);
            $asset->user_id = id_encode($asset->user_id);

            return $asset;
        });

        return response()->json([
            'assets' => $assets
        ]);
    }

    public function showExportForm(): RedirectResponse|View
    {
        $hotels = Hotel::where('user_id', id_parent())
            ->get(fields_get('hotels'));

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('hotels.index');
        }

        return view('app.assets.export', compact('hotels'));
    }

    public function export(AssetsReportQuery $request): BinaryFileResponse
    {
        $hotels = Hotel::where('user_id', id_parent())
            ->when($request->filled('hotel'), function ($query) use ($request) {
                $query->where('id', id_decode($request->hotel));
            })
            ->with([
                'assets' => function($query) {
                    $query->select(fields_get('assets'));
                },
                'assets.room' => function ($query)
                {
                    $query->select(fields_get('rooms'));
                }
            ])->get(fields_get('hotels'));

        return Excel::download(new AssetsReport($hotels), trans('assets.title') . '.xlsx');
    }

    public function assignment(string $room): View
    {
        $room = Room::where('id', id_decode($room))
            ->with([
                'hotel' => function ($query) {
                    $query->select(fields_get('hotels'));
                }
            ])
            ->firstOrFail(fields_get('rooms'));

        $assets = Asset::where('hotel_id', $room->hotel_id)
            ->doesntHave('room')
            ->get(fields_get('assets'));

        return view('app.assets.assign', compact('room', 'assets'));
    }

    public function assign(AssignAsset $request, string $room): RedirectResponse
    {
        $room = Room::findOrFail(id_decode($room), fields_get('rooms'));

        $asset = Asset::where('hotel_id', $room->hotel_id)
            ->where('id', $request->input('asset'))
            ->doesntHave('room')
            ->firstOrFail(fields_get('assets'));

        $asset->location = null;
        $asset->room()->associate($room);
        $asset->saveOrFail();

        flash(trans('common.updatedSuccessfully'))->success();

        return redirect()->route('assets.assignment', [
            'room' => $room->hash,
        ]);
    }
}
