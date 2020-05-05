<?php

namespace App\Http\Controllers;

use App\Exports\ServiceReport;
use App\Exports\ServicesReport;
use App\Helpers\{Chart, Fields, Parameter};
use App\User;
use App\Welkome\{Hotel, Service};
use Illuminate\Http\Request;
use App\Http\Requests\{DateRangeQuery, ReportQuery, StoreService, UpdateService};
use Maatwebsite\Excel\Facades\Excel;

class DiningServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hotels = Hotel::where('user_id', id_parent())
            ->with([
                'services' => function ($query)
                {
                    $query->select(Fields::get('services'))
                        ->where('is_dining_service', true);
                }
            ])->get(Fields::get('hotels'));

        $hotels = $hotels->map(function ($hotel)
        {
            $hotel->user_id = id_encode($hotel->user_id);
            $hotel->main_hotel = empty($hotel->main_hotel) ? null : id_encode($hotel->main_hotel);
            $hotel->services = $hotel->services->map(function ($service)
            {
                $service->hotel_id = id_encode($service->hotel_id);
                $service->user_id = id_encode($service->user_id);

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
        $hotels = Hotel::where('user_id', id_parent())
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
        $service->hotel()->associate(id_decode($request->hotel));

        if ($service->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('dining.show', [
                'id' => id_encode($service->id)
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
        $service = User::find(id_parent(), ['id'])->services()
            ->where('id', id_decode($id))
            ->where('is_dining_service', true)
            ->first(Fields::get('services'));

        if (empty($service)) {
            abort(404);
        }

        $service->load([
            'hotel' => function($query) {
                $query->select(Fields::get('hotels'));
            },
            'vouchers' => function ($query) {
                $query->select(Fields::parsed('vouchers'))
                    ->orderBy('vouchers.created_at', 'DESC')
                    ->whereYear('vouchers.created_at', \date('Y'))
                    ->withPivot(['quantity', 'value']);
            }
        ]);

        $data = Chart::create($service->vouchers)
            ->countItems()
            ->get();

        return view('app.dining.show', compact('service', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = User::find(id_parent(), ['id'])->services()
            ->where('id', id_decode($id))
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
        $service = User::find(id_parent(), ['id'])->services()
            ->where('id', id_decode($id))
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
                'id' => id_encode($service->id)
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
        $service = User::find(id_parent(), ['id'])->services()
            ->where('id', id_decode($id))
            ->where('is_dining_service', true)
            ->first(Fields::get('services'));

        if (empty($service)) {
            abort(404);
        }

        $service->load([
            'vouchers' => function ($query)
            {
                $query->select('id');
            },
        ]);

        if ($service->vouchers->count() > 0) {
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
            $query = Parameter::clean($request->get('query', null));

            $services = Service::where('hotel_id', id_decode($request->hotel))
                ->where('user_id', id_parent())
                ->where('is_dining_service', true)
                ->whereLike('description', $query)
                ->get(Fields::get('services'));

            $services = $services->map(function ($service)
            {
                $service->hotel_id = id_encode($service->hotel_id);
                $service->user_id = id_encode($service->user_id);

                return $service;
            });

            return response()->json([
                'services' => $services->toJson()
            ]);
        }

        abort(405);
    }

    /**
     * Display the service report form to query between dates.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showServiceReportForm($id)
    {
        $service = User::find(id_parent(), ['id'])->services()
            ->where('id', id_decode($id))
            ->where('is_dining_service', true)
            ->first(Fields::get('services'));

        if (empty($service)) {
            abort(404);
        }

        $service->load([
            'hotel' => function ($query)
            {
                $query->select(['id', 'business_name']);
            }
        ]);

        return view('app.dining.dining-service-report', compact('service'));
    }

    /**
     * Export Service report in an excel document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function exportServiceReport(DateRangeQuery $request, $id)
    {
        $service = User::find(id_parent(), ['id'])->services()
            ->where('id', id_decode($id))
            ->where('is_dining_service', true)
            ->first(Fields::get('services'));

        if (empty($service)) {
            abort(404);
        }

        $service->load([
            'hotel' => function ($query)
            {
                $query->select(['id', 'business_name']);
            },
            'vouchers' => function ($query) use ($request)
            {
                $query->select(Fields::parsed('vouchers'))
                    ->whereBetween('vouchers.created_at', [$request->start, $request->end])
                    ->orderBy('vouchers.created_at', 'DESC')
                    ->withPivot('quantity', 'value');
            },
            'vouchers.company' => function ($query) use ($request)
            {
                $query->select(Fields::parsed('companies'));
            },
        ]);

        if ($service->vouchers->isEmpty()) {
            flash(trans('common.without.results'))->info();

            return redirect()->route('dining.service.report', ['id' => id_encode($service->id)]);
        }

        return Excel::download(new ServiceReport($service), trans('dining.item') . '.xlsx');
    }

    /**
     * Display the report form to query between dates and hotels.
     *
     * @return \Illuminate\Http\Response
     */
    public function showReportForm()
    {
        $hotels = Hotel::where('user_id', id_parent())
            ->get(Fields::get('hotels'));

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('dining.index');
        }

        return view('app.dining.report', compact('hotels'));
    }

    /**
     * Export the services report in an excel document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportReport(ReportQuery $request)
    {
        $query = Hotel::query();
        $query->where('user_id', id_parent());

        if (!empty($request->hotel)) {
            $query->where('id', id_decode($request->hotel));
        }

        $query->with([
            'services' => function($query) {
                $query->select(Fields::get('services'))
                    ->where('is_dining_service', true);
            },
            'services.vouchers' => function ($query) use ($request)
            {
                $query->select(Fields::parsed('vouchers'))
                    ->whereBetween('vouchers.created_at', [$request->start, $request->end])
                    ->orderBy('vouchers.created_at', 'DESC')
                    ->withPivot('quantity', 'value');
            },
            'services.vouchers.company' => function ($query) use ($request)
            {
                $query->select(Fields::parsed('companies'));
            }
        ]);

        $hotels = $query->get(Fields::get('hotels'));

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return back();
        }

        return Excel::download(new ServicesReport($hotels), trans('dining.title') . '.xlsx');
    }
}
