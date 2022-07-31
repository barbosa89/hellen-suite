<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Room;
use App\Models\Asset;
use App\Models\Hotel;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use App\Exports\AssetsReport;
use App\Contracts\RoomRepository;
use App\Http\Requests\AssignAsset;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\AssetsReportQuery;
use App\Http\Requests\StoreAsset;
use App\Http\Requests\StoreMaintenance;
use App\Http\Requests\UpdateAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetController extends Controller
{
    private RoomRepository $room;

    public function __construct(RoomRepository $room)
    {
        $this->room = $room;
    }

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $asset = User::find(id_parent(), ['id'])->assets()
            ->where('id', id_decode($id))
            ->first(['id']);

        if (empty($asset)) {
            abort(404);
        }

        if ($asset->delete()) {
            flash(trans('common.deletedSuccessfully'))->success();

            return redirect()->route('assets.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('assets.index');
    }

    /**
     * Return a rooms list by hotel ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        if ($request->ajax()) {
            $query = clean_param($request->get('query', null));

            $assets = Asset::where('hotel_id', id_decode($request->hotel))
                ->where('user_id', id_parent())
                ->whereLike(['number', 'description', 'brand', 'model', 'serial_number', 'location'], $query)
                ->get(fields_get('assets'));

            $assets = $assets->map(function ($asset)
            {
                $asset->hotel_id = id_encode($asset->hotel_id);
                $asset->user_id = id_encode($asset->user_id);

                return $asset;
            });

            return response()->json([
                'assets' => $assets->toJson()
            ]);
        }

        abort(404);
    }

    /**
     * Display the report form to query between dates and hotels.
     *
     * @return \Illuminate\Http\Response
     */
    public function showReportForm()
    {
        $hotels = Hotel::where('user_id', id_parent())
            ->get(fields_get('hotels'));

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('hotels.index');
        }

        return view('app.assets.report', compact('hotels'));
    }

    /**
     * Export the props report in an excel document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(AssetsReportQuery $request)
    {
        if (empty($request->get('hotel', null))) {
            $hotels = Hotel::where('user_id', id_parent())
                ->with([
                    'assets' => function($query) {
                        $query->select(fields_get('assets'));
                    },
                    'assets.room' => function ($query)
                    {
                        $query->select(fields_get('rooms'));
                    }
                ])->get(fields_get('hotels'));
        } else {
            $hotels = Hotel::where('user_id', id_parent())
                ->where('id', id_decode($request->hotel))
                ->with([
                    'assets' => function($query) {
                        $query->select(fields_get('assets'));
                    },
                    'assets.room' => function ($query)
                    {
                        $query->select(fields_get('rooms'));
                    }
                ])->get(fields_get('hotels'));
        }

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('hotels.index');
        }

        return Excel::download(new AssetsReport($hotels), trans('assets.title') . '.xlsx');
    }

    /**
     * Display the maintenance form to add new record.
     *
     * @return \Illuminate\Http\Response
     */
    public function showMaintenanceForm($id)
    {
        $asset = User::find(id_parent(), ['id'])->assets()
            ->where('id', id_decode($id))
            ->first(fields_get('assets'));

        if (empty($asset)) {
            abort(404);
        }

        $asset->load([
            'room' => function ($query) {
                $query->select('id', 'number');
            },
            'hotel' => function ($query) {
                $query->select('id', 'business_name');
            }
        ]);

        return view('app.assets.maintenance', compact('asset'));
    }

    /**
     * Store the asset maintenance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function maintenance(StoreMaintenance $request, $id)
    {
        $asset = User::find(id_parent(), ['id'])->assets()
            ->where('id', id_decode($id))
            ->first(fields_get('assets'));

        if (empty($asset)) {
            abort(404);
        }

        $maintenance = new Maintenance();
        $maintenance->date = $request->date;
        $maintenance->commentary = $request->commentary;
        $maintenance->value = $request->get('value', null);
        $maintenance->user()->associate(id_parent());

        if ($request->hasFile('invoice')) {
            $path = $request->file('invoice')->storeAs(
                'public',
                time() . "_" . $request->file('invoice')->getClientOriginalName()
            );

            $maintenance->invoice = $path;
        }

        if ($asset->maintenances()->save($maintenance)) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('assets.show', [
                'id' => id_encode($asset->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Display the maintenance form to edit record.
     *
     * @return \Illuminate\Http\Response
     */
    public function showMaintenanceEditForm($id, $maintenance)
    {
        $asset = User::find(id_parent(), ['id'])->assets()
            ->where('id', id_decode($id))
            ->first(fields_get('assets'));

        if (empty($asset)) {
            abort(404);
        }

        $asset->load([
            'room' => function ($query) {
                $query->select('id', 'number');
            },
            'hotel' => function ($query) {
                $query->select('id', 'business_name');
            },
            'maintenances' => function ($query) use ($id, $maintenance)
            {
                $query->select(fields_get('maintenances'))
                    ->where('maintainable_id', id_decode($id))
                    ->where('id', id_decode($maintenance));
            }
        ]);

        return view('app.assets.maintenance-edit', compact('asset'));
    }

    /**
     * Store the asset maintenance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateMaintenance(StoreMaintenance $request, $id, $maintenance)
    {
        $asset = User::find(id_parent(), ['id'])->assets()
            ->where('id', id_decode($id))
            ->first(fields_get('assets'));

        if (empty($asset)) {
            abort(404);
        }

        $asset->load([
            'maintenances' => function ($query) use ($id, $maintenance)
            {
                $query->select(fields_get('maintenances'))
                    ->where('maintainable_id', id_decode($id))
                    ->where('id', id_decode($maintenance));
            }
        ]);

        $maintenance = $asset->maintenances->first();
        $maintenance->date = $request->date;
        $maintenance->commentary = $request->commentary;
        $maintenance->value = $request->get('value', null);

        if ($request->hasFile('invoice')) {
            if (!empty($maintenance->invoice)) {
                Storage::delete($maintenance->invoice);
            }

            $path = $request->file('invoice')->storeAs(
                'public',
                time() . "_" . $request->file('invoice')->getClientOriginalName()
            );
            $maintenance->invoice = $path;
        }

        if ($maintenance->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('assets.show', [
                'id' => id_encode($asset->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyMaintenance($id, $maintenanceId)
    {
        $maintenance = Maintenance::where('user_id', id_parent())
            ->where('id', id_decode($maintenanceId))
            ->where('maintainable_id', id_decode($id))
            ->first(fields_get('maintenances'));

        if (empty($maintenance)) {
            abort(404);
        }

        $voucher = $maintenance->invoice;

        if ($maintenance->delete()) {
            Storage::delete($voucher);

            flash(trans('common.deletedSuccessfully'))->success();

            return back();
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * @param string $room
     * @return \Illuminate\Http\Response
     */
    public function assignment(string $room)
    {
        $room = $this->room->find(id_decode($room));

        $assets = Asset::where('hotel_id', $room->hotel->id)
            ->doesntHave('room')
            ->get(fields_get('assets'));

        return view('app.assets.assign', compact('room', 'assets'));
    }

    /**
     * @param \App\Http\Requests\AssignAsset $request
     * @param string $room
     * @return \Illuminate\Http\Response
     */
    public function assign(AssignAsset $request, string $room)
    {
        try {
            $room = $this->room->find(id_decode($room));

            $asset = Asset::where('hotel_id', $room->hotel->id)
                ->where('id', $request->asset)
                ->doesntHave('room')
                ->first(fields_get('assets'));

            $asset->location = null;
            $asset->room()->associate($room);
            $asset->saveOrFail();

            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('assets.assignment', [
                'room' => id_encode($room->id),
            ]);
        } catch (\Throwable $th) {
            flash(trans('common.error'))->error();

            return redirect()->route('assets.assignment', [
                'room' => id_encode($room->id),
            ]);
        }
    }
}
