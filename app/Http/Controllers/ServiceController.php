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

class ServiceController extends Controller
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
                    $query->select(Fields::get('services'));
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

        return view('app.services.index', compact('hotels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hotels = Hotel::where('user_id', Id::parent())
            ->whereStatus(true)
            ->get(Fields::get('hotels'));

        if ($hotels->isEmpty()) {
            flash('No hay hoteles creados')->info();

            return back();
        }

        return view('app.services.create', compact('hotels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreService $request)
    {
        $service = new service();
        $service->description = $request->description;
        $service->price = (float) $request->price;
        $service->user()->associate(auth()->user()->id);
        $service->hotel()->associate(Id::get($request->hotel));

        if ($service->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('services.show', [
                'id' => Hashids::encode($service->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('services.index');
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
            ->with([
                'hotel' => function($query) {
                    $query->select(Fields::get('hotels'));
                }
            ])->first(Fields::get('services'));

        if (empty($service)) {
            abort(404);
        }

        return view('app.services.show', compact('service'));
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
            ->with([
                'hotel' => function($query) {
                    $query->select(Fields::get('hotels'));
                }
            ])->first(Fields::get('services'));

        if (empty($service)) {
            abort(404);
        }

        return view('app.services.edit', compact('service'));
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
            ->first(Fields::get('services'));

        if (empty($service)) {
            abort(404);
        }

        $service->description = $request->description;
        $service->price = (float) $request->price;

        if ($service->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('services.show', [
                'id' => Hashids::encode($service->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('services.index');
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

                return redirect()->route('services.index');
            }
        } else {
            if ($service->delete()) {
                flash(trans('common.deletedSuccessfully'))->success();

                return redirect()->route('services.index');
            }
        }

        flash(trans('common.error'))->error();

        return redirect()->route('services.index');
    }

    /**
     * Return price of resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function total(Request $request)
    {
        if ($request->ajax()) {
            $service = Service::find(Id::get($request->element), ['id', 'price']);

            if (empty($service)) {
                return response()->json(['value' => null]);
            } else {
                $value = (int) $request->quantity * $service->price;
                $value = number_format($value, 2, ',', '.');

                return response()->json(['value' => $value]);
            }
        }

        abort(404);
    }

    /**
     * Toggle status for the specified resource from storage.
     *
     * @param  string   $id
     * @return \Illuminate\Http\Response
     */
    public function toggle($id)
    {
        $service = User::find(Id::parent(), ['id'])->services()
            ->where('id', Id::get($id))
            ->first(Fields::get('services'));

        if (empty($service)) {
            return abort(404);
        }

        $service->status = !$service->status;

        if ($service->save()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect(url()->previous());
        }

        flash(trans('common.error'))->error();

        return redirect(url()->previous());
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

        abort(404);
    }
}
