<?php

namespace App\Http\Controllers;

use App\Helpers\Id;
use App\Welkome\Prop;
use App\Welkome\Hotel;
use App\Helpers\Fields;
use App\Helpers\Input;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProp;
use Vinkla\Hashids\Facades\Hashids;

class PropController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hotels = Hotel::where('user_id', Id::parent())
            ->with([
                'props' => function ($query)
                {
                    $query->select(Fields::get('props'));
                }
            ])->get(Fields::get('hotels'));

        $hotels = $hotels->map(function ($hotel)
        {
            $hotel->user_id = Hashids::encode($hotel->user_id);
            $hotel->main_hotel = empty($hotel->main_hotel) ? null : Hashids::encode($hotel->main_hotel);
            $hotel->props = $hotel->props->map(function ($prop)
            {
                $prop->hotel_id = Hashids::encode($prop->hotel_id);
                $prop->user_id = Hashids::encode($prop->user_id);

                return $prop;
            });

            return $hotel;
        });

        return view('app.props.index', compact('hotels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hotels = Hotel::where('user_id', Id::parent(), ['id'])
            ->where('status', true)
            ->get(Fields::get('hotels'));

        if($hotels->isEmpty()) {
            flash('No hay hoteles creados')->info();

            return redirect()->route('props.index');
        }
        return view('app.props.create', compact('hotels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProp $request)
    {
        $asset = new Prop();
        $asset->description = $request->description;
        $asset->quantity = $request->quantity;
        $asset->user()->associate(Id::parent(), ['id']);
        $asset->hotel()->associate(Id::get($request->hotel));

        if ($asset->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('props.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('props.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $asset = User::find(Id::parent(), ['id'], ['id'])->props()
            ->where('id', Id::get($id))
            ->first(Fields::get('props'));

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

        return view('app.props.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $asset = User::find(Id::parent(), ['id'], ['id'])->props()
            ->where('id', Id::get($id))
            ->first(Fields::get('props'));

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

        $hotels = Hotel::where('user_id', Id::parent(), ['id'])
            ->where('id', '!=', $asset->hotel->id)
            ->where('status', true)
            ->get(Fields::get('hotels'));

        return view('app.props.edit', compact('asset', 'hotels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProp $request, $id)
    {
        $asset = User::find(Id::parent(), ['id'], ['id'])->props()
            ->where('id', Id::get($id))
            ->where('hotel_id', Id::get($request->hotel))
            ->first(Fields::get('props'));

        if (empty($asset)) {
            abort(404);
        }

        $asset->description = $request->description;
        $asset->brand = $request->get('brand', null);
        $asset->model = $request->get('model', null);
        $asset->reference = $request->get('reference', null);
        $asset->location = $request->get('location', null);
        $asset->hotel()->associate(Id::get($request->hotel));

        if (empty($request->get('room', null))) {
            $asset->room()->dissociate();
        } else {
            $room = Room::where('id', Id::get($request->room))
                ->where('hotel_id', Id::get($request->hotel))
                ->where('user_id', Id::parent(), ['id'])
                ->first(['id']);

            if (empty($room)) {
                flash('La habitación seleccionada no corresponde al hotel')->error();

                return back();
            }

            $asset->room()->associate($room->id);
        }

        if ($asset->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('props.show', [
                'id' => Hashids::encode($asset->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('props.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $asset = User::find(Id::parent(), ['id'], ['id'])->props()
            ->where('id', Id::get($id))
            ->first(['id']);

        if (empty($asset)) {
            abort(404);
        }

        if ($asset->delete()) {
            flash(trans('common.deletedSuccessfully'))->success();

            return redirect()->route('props.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('props.index');
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
            $query = Input::clean($request->get('query', null));

            $props = Prop::where('hotel_id', Id::get($request->hotel))
                ->whereLike('description', $query)
                ->get(Fields::get('props'));

            $props = $props->map(function ($prop)
            {
                $prop->hotel_id = Hashids::encode($prop->hotel_id);
                $prop->user_id = Hashids::encode($prop->user_id);

                return $prop;
            });

            return response()->json([
                'props' => $props->toJson()
            ]);
        }

        abort(404);
    }
}
