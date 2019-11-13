<?php

namespace App\Http\Controllers;

use App\Exports\AssetsReport;
use App\User;
use App\Helpers\Id;
use App\Welkome\Room;
use App\Welkome\Asset;
use App\Welkome\Hotel;
use App\Helpers\Fields;
use App\Helpers\Input;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Requests\{AssetsReportQuery, StoreAsset, UpdateAsset};
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
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
                'assets' => function ($query)
                {
                    $query->select(Fields::get('assets'));
                }
            ])->get(Fields::get('hotels'));

        $hotels = $hotels->map(function ($hotel)
        {
            $hotel->user_id = Hashids::encode($hotel->user_id);
            $hotel->main_hotel = empty($hotel->main_hotel) ? null : Hashids::encode($hotel->main_hotel);
            $hotel->assets = $hotel->assets->map(function ($asset)
            {
                $asset->hotel_id = Hashids::encode($asset->hotel_id);
                $asset->user_id = Hashids::encode($asset->user_id);
                $asset->room_id = Hashids::encode($asset->room_id);

                return $asset;
            });

            return $hotel;
        });

        return view('app.assets.index', compact('hotels'));
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

            return redirect()->route('assets.show', [
                'id' => Hashids::encode($asset->id)
            ]);
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
                $query->select('id', 'number', 'description');
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

        if (empty($request->get('room', null))) {
            $asset->room()->dissociate();
        } else {
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

            return redirect()->route('assets.show', [
                'id' => Hashids::encode($asset->id)
            ]);
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
            $query = Input::clean($request->get('query', null));

            $assets = Asset::where('hotel_id', Id::get($request->hotel))
                ->where('user_id', Id::parent())
                ->whereLike(['number', 'description', 'brand', 'model', 'reference', 'location'], $query)
                ->get(Fields::get('assets'));

            $assets = $assets->map(function ($asset)
            {
                $asset->hotel_id = Hashids::encode($asset->hotel_id);
                $asset->user_id = Hashids::encode($asset->user_id);

                return $asset;
            });

            return response()->json([
                'assets' => $assets->toJson()
            ]);
        }

        abort(404);
    }

    /**
     * Export Prop report in an excel document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function propReport(PropsReportQuery $request, $id)
    {
        $prop = User::find(Id::parent(), ['id'])->props()
            ->where('id', Id::get($id))
            ->first(Fields::get('props'));

        if (empty($prop)) {
            abort(404);
        }

        $prop->load([
            'hotel' => function ($query)
            {
                $query->select(['id', 'business_name']);
            },
            'transactions' => function ($query) use ($request)
            {
                $query->select(Fields::get('transactions'))
                    ->whereBetween('created_at', [$request->start, $request->end])
                    ->orderBy('created_at', 'DESC');
            }
        ]);

        if ($prop->transactions->isEmpty()) {
            flash('No hay información en las fechas indicadas')->info();

            return redirect()->route('props.prop.report', ['id' => Hashids::encode($prop->id)]);
        }

        return Excel::download(new PropReport($prop), trans('props.prop') . '.xlsx');
    }

    /**
     * Display the report form to query between dates and hotels.
     *
     * @return \Illuminate\Http\Response
     */
    public function showReportForm()
    {
        $hotels = Hotel::where('user_id', Id::parent())
            ->get(Fields::get('hotels'));

        if($hotels->isEmpty()) {
            flash('No hay hoteles creados')->info();

            return redirect()->route('assets.index');
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
            $hotels = Hotel::where('user_id', Id::parent())
                ->with([
                    'assets' => function($query) {
                        $query->select(Fields::get('assets'));
                    },
                    'assets.room' => function ($query)
                    {
                        $query->select(Fields::get('rooms'));
                    }
                ])->get(Fields::get('hotels'));
        } else {
            $hotels = Hotel::where('user_id', Id::parent())
                ->where('id', Id::get($request->hotel))
                ->with([
                    'assets' => function($query) {
                        $query->select(Fields::get('assets'));
                    },
                    'assets.room' => function ($query)
                    {
                        $query->select(Fields::get('rooms'));
                    }
                ])->get(Fields::get('hotels'));
        }

        if($hotels->isEmpty()) {
            flash('No hay hoteles creados')->info();

            return back();
        }

        return Excel::download(new AssetsReport($hotels), trans('assets.title') . '.xlsx');
    }
}
