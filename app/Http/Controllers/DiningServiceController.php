<?php

namespace App\Http\Controllers;

use App\User;
use App\Helpers\Id;
use App\Welkome\Hotel;
use App\Helpers\Fields;
use App\Helpers\Input;
use App\Welkome\Service;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Requests\{StoreService, UpdateService};

class DiningServiceController extends Controller
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
                'services' => function ($query)
                {
                    $query->select(Fields::get('services'))
                        ->where('is_dining_service', true);
                }
            ])->get(Fields::get('hotels'));

        $hotels = $hotels->map(function ($hotel)
        {
            $hotel->user_id = Hashids::encode($hotel->user_id);
            $hotel->main_hotel = empty($hotel->main_hotel) ? null : Hashids::encode($hotel->main_hotel);
            $hotel->services = $hotel->services->map(function ($service)
            {
                $service->hotel_id = Hashids::encode($service->hotel_id);
                $service->user_id = Hashids::encode($service->user_id);

                return $service;
            });

            return $hotel;
        });

        // Check if is empty
        if ($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            if (auth()->user()->can('hotels.index')) {
                return redirect()->route('hotels.index');
            }

            return redirect()->route('home');
        }

        return view('app.dining.index', compact('hotels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // TODO: Limitar la sede del hotel
        $hotels = Hotel::where('user_id', Id::parent())
            ->whereStatus(true)
            ->get(Fields::get('hotels'));

        if ($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return back();
        }

        return view('app.dining.create', compact('hotels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreService $request)
    {
        $service = new Service();
        $service->description = $request->description;
        $service->price = (float) $request->price;
        $service->is_dining_service = true;
        $service->user()->associate(auth()->user()->id);
        $service->hotel()->associate(Id::get($request->hotel));

        if ($service->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('dining.show', [
                'id' => Hashids::encode($service->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('dining.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = User::find(Id::parent(), ['id'])->services()
            ->where('id', Id::get($id))
            ->where('is_dining_service', true)
            ->with([
                'hotel' => function($query) {
                    $query->select(Fields::get('hotels'));
                }
            ])->first(Fields::get('services'));

        if (empty($service)) {
            abort(404);
        }

        return view('app.dining.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = User::find(Id::parent(), ['id'])->services()
            ->where('id', Id::get($id))
            ->where('is_dining_service', true)
            ->with([
                'hotel' => function($query) {
                    $query->select(Fields::get('hotels'));
                }
            ])->first(Fields::get('services'));

        if (empty($service)) {
            abort(404);
        }

        return view('app.dining.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateService $request, $id)
    {
        $service = User::find(Id::parent(), ['id'])->services()
            ->where('id', Id::get($id))
            ->where('is_dining_service', true)
            ->first(Fields::get('services'));

        if (empty($service)) {
            abort(404);
        }

        $service->description = $request->description;
        $service->price = (float) $request->price;

        if ($service->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('dining.show', [
                'id' => Hashids::encode($service->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('dining.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $service = User::find(Id::parent(), ['id'])->services()
            ->where('id', Id::get($id))
            ->where('is_dining_service', true)
            ->first(Fields::get('services'));

        if (empty($service)) {
            abort(404);
        }

        $service->load([
            'invoices' => function ($query)
            {
                $query->select('id');
            },
        ]);

        if ($service->invoices->count() > 0) {
            $service->status = 0;

            if ($service->save()) {
                flash(trans('services.wasDisabled'))->success();

                return redirect()->route('dining.index');
            }
        } else {
            if ($service->delete()) {
                flash(trans('common.deletedSuccessfully'))->success();

                return redirect()->route('dining.index');
            }
        }

        flash(trans('common.error'))->error();

        return redirect()->route('dining.index');
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

            $services = Service::where('hotel_id', Id::get($request->hotel))
                ->where('user_id', Id::parent())
                ->where('is_dining_service', true)
                ->whereLike('description', $query)
                ->get(Fields::get('services'));

            $services = $services->map(function ($service)
            {
                $service->hotel_id = Hashids::encode($service->hotel_id);
                $service->user_id = Hashids::encode($service->user_id);

                return $service;
            });

            return response()->json([
                'services' => $services->toJson()
            ]);
        }

        abort(405);
    }
}
