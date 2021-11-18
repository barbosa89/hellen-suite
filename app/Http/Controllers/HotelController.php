<?php

namespace App\Http\Controllers;

use App\Helpers\Chart;
use App\User;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Requests\{StoreHotel, UpdateHotel};
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hotels = Hotel::where('user_id', id_parent())
            ->latest()
            ->paginate(config('settings.paginate', fields_get('hotels')));

        return view('app.hotels.index', compact('hotels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hotels = User::find(auth()->user()->id)->hotels()
            ->where('main_hotel', null)
            ->get(fields_get('hotels'));

        return view('app.hotels.create', compact('hotels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHotel $request)
    {
        $hotel = new Hotel();
        $hotel->business_name = $request->business_name;
        $hotel->tin = $request->tin;
        $hotel->address = $request->address;
        $hotel->phone = $request->phone;
        $hotel->mobile = $request->mobile;
        $hotel->email = $request->email;
        $hotel->owner()->associate(auth()->user()->id);

        if (!empty($request->main_hotel)) {
            $hotel->main_hotel = id_decode($request->main_hotel);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->storeAs(
                'public',
                time() . "_" . $request->file('image')->getClientOriginalName()
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

    /**
     * Display the specified resource.
     *
     *
     * @param  string   $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $hotel = User::find(auth()->user()->id)->hotels()
            ->where('id', id_decode($id))
            ->first(fields_get('hotels'));

        if (empty($hotel)) {
            return abort(404);
        }

        $hotel->load([
            'main' => function ($query)
            {
                $query->select(['id', 'business_name']);
            },
            'vouchers' => function ($query)
            {
                $query->select(fields_dotted('vouchers'))
                    ->whereYear('vouchers.created_at', date('Y'))
                    ->orderBy('vouchers.created_at', 'DESC');
            }
        ]);

        $data = Chart::create($hotel->vouchers)
            ->addValues()
            ->get();

        return view('app.hotels.show', compact('hotel', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string   $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $hotel = User::find(auth()->user()->id)->hotels()
            ->where('id', id_decode($id))
            ->with([
                'main' => function ($query)
                {
                    $query->select(['id', 'business_name']);
                }
            ])->first(fields_get('hotels'));

        if (empty($hotel)) {
            return abort(404);
        }

        return view('app.hotels.edit', compact('hotel'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string   $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHotel $request, $id)
    {
        $hotel = User::find(auth()->user()->id, ['id'])->hotels()
            ->where('id', id_decode($id))
            ->first(fields_get('hotels'));

        if (empty($hotel)) {
            return abort(404);
        }

        $hotel->address = $request->address;
        $hotel->phone = $request->phone;
        $hotel->mobile = $request->mobile;
        $hotel->email = $request->email;

        if ($request->hasFile('image')) {
            if (!empty($hotel->image)) {
                Storage::delete($hotel->image);
            }

            $path = $request->file('image')->storeAs(
                'public',
                time() . "_" . $request->file('image')->getClientOriginalName()
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  string   $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $hotel = User::find(auth()->user()->id)->hotels()
            ->where('id', id_decode($id))
            ->whereDoesntHave('headquarters', function ($query)
            {
                $query->select(['id', 'main_hotel']);
            })
            ->whereDoesntHave('vouchers', function ($query)
            {
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

    /**
     * Toggle status for the specified resource from storage.
     *
     * @param  string   $id
     * @return \Illuminate\Http\Response
     */
    public function toggle($id)
    {
        $hotel = User::find(auth()->user()->id)->hotels()
            ->where('id', id_decode($id))
            ->first(fields_get('hotels'));

        if (empty($hotel)) {
            return abort(404);
        }

        $hotel->status = !$hotel->status;

        if ($hotel->save()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return back();
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Return a hotels list different to ID received.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getDifferentTo(Request $request)
    {
        if ($request->ajax()) {
            $hotels = Hotel::where('id', '!=', id_decode($request->hotel))
                ->where('user_id', id_parent())
                ->get(['id', 'business_name']);

            return response()->json([
                'hotels' => $hotels->toJson()
            ]);
        }

        abort(404);
    }

    /**
     * Return assigned hotel list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAssigned()
    {
        // Using assigned scoped
        $hotels = Hotel::assigned()->get(fields_get('hotels'));

        return response()->json([
            'hotels' =>$hotels
        ]);
    }
}
