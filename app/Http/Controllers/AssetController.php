<?php

namespace App\Http\Controllers;

use App\Helpers\Fields;
use App\User;
use App\Helpers\Id;
use App\Welkome\Room;
use App\Welkome\Asset;
use Illuminate\Http\Request;
use App\Http\Requests\{StoreAsset, UpdateAsset};
use App\Welkome\Hotel;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assets = User::find(Id::parent(), ['id'])->assets()
            ->paginate(config('welkome.paginate'), Fields::get('assets'))
            ->sortByDesc('created_at');

        return view('app.assets.index', compact('assets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hotels = Hotel::where('user_id', Id::parent())
            ->where('status', true)
            ->with([
                'rooms' => function ($query)
                {
                    $query->select(Fields::get('rooms'));
                }
            ])->get(Fields::get('hotels'));

        if($hotels->isEmpty()) {
            flash('No hay hoteles creados')->info();

            return redirect()->route('assets.index');
        }

        $rooms = $hotels->sum(function ($hotel)
        {
            return $hotel->rooms()->count();
        });

        if($rooms == 0) {
            flash('No hay habitaciones creadas')->info();

            return redirect()->route('assets.index');
        }

        return view('app.assets.create', compact('hotels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAsset $request)
    {
        $asset = new Asset();
        $asset->number = $request->number;
        $asset->description = $request->description;
        $asset->brand = $request->get('brand', null);
        $asset->model = $request->get('model', null);
        $asset->reference = $request->get('reference', null);
        $asset->location = $request->get('location', null);
        $asset->user()->associate(Id::parent());
        $asset->hotel()->associate(Id::get($request->hotel));

        if (!empty($request->get('room', null))) {
            $room = Room::where('id', Id::get($request->room))
                ->where('hotel_id', Id::get($request->hotel))
                ->where('user_id', Id::parent())
                ->first(['id']);

            if (empty($room)) {
                flash('La habitación seleccionada no corresponde al hotel')->error();

                return back();
            }

            $asset->room()->associate($room->id);
        }

        if ($asset->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('assets.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('assets.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $asset = User::find(Id::parent(), ['id'])->assets()
            ->where('id', Id::get($id))
            ->first(Fields::get('assets'));

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

        return view('app.assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $asset = User::find(Id::parent(), ['id'])->assets()
            ->where('id', Id::get($id))
            ->first(Fields::get('assets'));

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
            'hotel.rooms' => function ($query) {
                $query->select('id', 'number', 'hotel_id');
            },
        ]);

        $hotels = Hotel::where('user_id', Id::parent())
            ->where('id', '!=', $asset->hotel->id)
            ->where('status', true)
            ->get(Fields::get('hotels'));

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
        $asset = User::find(Id::parent(), ['id'])->assets()
            ->where('id', Id::get($id))
            ->where('hotel_id', Id::get($request->hotel))
            ->first(Fields::get('assets'));

        if (empty($asset)) {
            abort(404);
        }

        $asset->description = $request->description;
        $asset->brand = $request->get('brand', null);
        $asset->model = $request->get('model', null);
        $asset->reference = $request->get('reference', null);
        $asset->location = $request->get('location', null);
        $asset->hotel()->associate(Id::get($request->hotel));

        if (!empty($request->get('room', null))) {
            $room = Room::where('id', Id::get($request->room))
                ->where('hotel_id', Id::get($request->hotel))
                ->where('user_id', Id::parent())
                ->first(['id']);

            if (empty($room)) {
                flash('La habitación seleccionada no corresponde al hotel')->error();

                return back();
            }

            $asset->room()->associate($room->id);
        }

        if ($asset->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('assets.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('assets.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $asset = User::find(Id::parent(), ['id'])->assets()
            ->where('id', Id::get($id))
            ->first([
                'id', 'number', 'description', 'brand', 'model', 'reference', 'location', 'user_id'
            ]);

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
}
