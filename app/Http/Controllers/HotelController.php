<?php

namespace App\Http\Controllers;

use App\Helpers\Chart;
use App\Http\Requests\StoreHotel;
use App\Http\Requests\UpdateHotel;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    public function index(): View
    {
        $hotels = Hotel::where('user_id', id_parent())
            ->latest('id')
            ->paginate(config('settings.paginate', fields_get('hotels')));

        return view('app.hotels.index', compact('hotels'));
    }

    public function create(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $hotels = $user->hotels()
            ->where('main_hotel', null)
            ->get(fields_get('hotels'));

        return view('app.hotels.create', compact('hotels'));
    }

    public function store(StoreHotel $request): RedirectResponse
    {
        $hotel = new Hotel;
        $hotel->business_name = $request->business_name;
        $hotel->tin = $request->tin;
        $hotel->address = $request->address;
        $hotel->phone = $request->phone;
        $hotel->mobile = $request->mobile;
        $hotel->email = $request->email;
        $hotel->owner()->associate(auth()->user()->id);

        if (! empty($request->main_hotel)) {
            $hotel->main_hotel = id_decode($request->main_hotel);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->storeAs(
                'public',
                time().'_'.$request->file('image')->getClientOriginalName()
            );
            $hotel->image = $path;
        }

        if ($hotel->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('hotels.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('hotels.index');
    }

    public function show(string $id): View
    {
        $hotel = User::find(auth()->user()->id)->hotels()
            ->where('id', id_decode($id))
            ->first(fields_get('hotels'));

        if (empty($hotel)) {
            abort(404);
        }

        $hotel->load([
            'main' => function ($query): void {
                $query->select(['id', 'business_name']);
            },
            'vouchers' => function ($query): void {
                $query->select(fields_dotted('vouchers'))
                    ->limit(20)
                    ->orderBy('vouchers.created_at', 'DESC');
            },
        ]);

        $data = Chart::create($hotel->vouchers)
            ->addValues()
            ->get();

        return view('app.hotels.show', compact('hotel', 'data'));
    }

    public function edit(string $id): View
    {
        $hotel = User::find(auth()->user()->id)->hotels()
            ->where('id', id_decode($id))
            ->with([
                'main' => function ($query): void {
                    $query->select(['id', 'business_name']);
                },
            ])->first(fields_get('hotels'));

        if (empty($hotel)) {
            abort(404);
        }

        return view('app.hotels.edit', compact('hotel'));
    }

    public function update(UpdateHotel $request, string $id): RedirectResponse
    {
        /** @var Hotel $hotel */
        $hotel = User::find(auth()->user()->id, ['id'])->hotels()
            ->where('id', id_decode($id))
            ->first(fields_get('hotels'));

        if (empty($hotel)) {
            abort(404);
        }

        $hotel->address = $request->address;
        $hotel->phone = $request->phone;
        $hotel->mobile = $request->mobile;
        $hotel->email = $request->email;

        if ($request->hasFile('image')) {
            if (! empty($hotel->image)) {
                Storage::delete($hotel->image);
            }

            $path = $request->file('image')->storeAs(
                'public',
                time().'_'.$request->file('image')->getClientOriginalName()
            );
            $hotel->image = $path;
        }

        if ($hotel->save()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('hotels.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('hotels.index');
    }

    public function destroy(string $id): RedirectResponse
    {
        /** @var Hotel $hotel */
        $hotel = User::find(auth()->user()->id)->hotels()
            ->where('id', id_decode($id))
            ->whereDoesntHave('headquarters', function ($query): void {
                $query->select(['id', 'main_hotel']);
            })
            ->whereDoesntHave('vouchers', function ($query): void {
                $query->select(['id', 'hotel_id']);
            })->first(fields_get('hotels'));

        if (empty($hotel)) {
            flash('El hotel que intenta eliminar, tiene registros asociados como sedes y recibos, intente con deshabilitar')->error();

            return redirect()->route('hotels.index');
        }

        $image = empty($hotel->image) ? null : $hotel->image;

        if ($hotel->delete()) {
            Storage::delete($image);
            flash(trans('common.deletedSuccessfully'))->success();

            return redirect()->route('hotels.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('hotels.index');
    }

    public function toggle(string $id): RedirectResponse
    {
        /** @var Hotel $hotel */
        $hotel = User::find(auth()->user()->id)->hotels()
            ->where('id', id_decode($id))
            ->first(fields_get('hotels'));

        if (empty($hotel)) {
            abort(404);
        }

        $hotel->status = ! $hotel->status;

        if ($hotel->save()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return back();
        }

        flash(trans('common.error'))->error();

        return back();
    }

    public function getDifferentTo(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $hotels = Hotel::where('id', '!=', id_decode($request->hotel))
                ->where('user_id', id_parent())
                ->get(['id', 'business_name']);

            return response()->json([
                'hotels' => $hotels->toJson(),
            ]);
        }

        abort(404);
    }

    public function getAssigned(): JsonResponse
    {
        $hotels = Hotel::assigned()->get(fields_get('hotels'));

        return response()->json([
            'hotels' => $hotels,
        ]);
    }
}
