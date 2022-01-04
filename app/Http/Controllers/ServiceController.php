<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Hotel;
use App\Helpers\Chart;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Exports\ServiceReport;
use Illuminate\Support\Carbon;
use App\Exports\ServicesReport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\{DateRangeQuery, ReportQuery, StoreService, UpdateService};

class ServiceController extends Controller
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
                    $query->select(fields_get('services'))
                        ->where('is_dining_service', false);
                }
            ])->get(fields_get('hotels'));

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

        return view('app.services.index', compact('hotels'));
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
            ->get(fields_get('hotels'));

        if ($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

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
        $service = new Service();
        $service->description = $request->description;
        $service->price = (float) $request->price;
        $service->user()->associate(auth()->user()->id);
        $service->hotel()->associate(id_decode($request->hotel));

        if ($service->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('services.show', [
                'id' => id_encode($service->id)
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
        $service = User::find(id_parent(), ['id'])->services()
            ->where('id', id_decode($id))
            ->where('is_dining_service', false)
            ->first(fields_get('services'));

        if (empty($service)) {
            abort(404);
        }

        $service->load([
            'hotel' => function($query) {
                $query->select(fields_get('hotels'));
            },
            'vouchers' => function ($query) {
                $query->select(fields_dotted('vouchers'))
                ->latest()
                ->limit(20)
                    ->withPivot(['quantity', 'value']);
            }
        ]);

        $data = Chart::create($service->vouchers)
            ->countItems()
            ->get();

        return view('app.services.show', compact('service', 'data'));
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
            ->where('is_dining_service', false)
            ->with([
                'hotel' => function($query) {
                    $query->select(fields_get('hotels'));
                }
            ])->first(fields_get('services'));

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
        $service = User::find(id_parent(), ['id'])->services()
            ->where('id', id_decode($id))
            ->where('is_dining_service', false)
            ->first(fields_get('services'));

        if (empty($service)) {
            abort(404);
        }

        $service->description = $request->description;
        $service->price = (float) $request->price;

        if ($service->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('services.show', [
                'id' => id_encode($service->id)
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
        $service = User::find(id_parent(), ['id'])->services()
            ->where('id', id_decode($id))
            ->where('is_dining_service', false)
            ->first(fields_get('services'));

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
    public function calculateTotal(Request $request)
    {
        if ($request->ajax()) {
            $service = Service::find(id_decode($request->element), ['id', 'price']);

            if (empty($service)) {
                return response()->json(['value' => null]);
            } else {
                $value = (int) $request->quantity * $service->price;
                $value = number_format($value, 2, ',', '.');

                return response()->json(['value' => $value]);
            }
        }

        abort(405);
    }

    /**
     * Toggle status for the specified resource from storage.
     *
     * @param  string   $id
     * @return \Illuminate\Http\Response
     */
    public function toggle($id)
    {
        $service = User::find(id_parent(), ['id'])->services()
            ->where('id', id_decode($id))
            ->first(fields_get('services'));

        if (empty($service)) {
            return abort(404);
        }

        $service->status = !$service->status;

        if ($service->save()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return back();
        }

        flash(trans('common.error'))->error();

        return back();
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

            $services = Service::where('hotel_id', id_decode($request->hotel))
                ->where('user_id', id_parent())
                ->where('is_dining_service', false)
                ->whereLike('description', $query)
                ->get(fields_get('services'));

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
            ->where('is_dining_service', false)
            ->first(fields_get('services'));

        if (empty($service)) {
            abort(404);
        }

        $service->load([
            'hotel' => function ($query)
            {
                $query->select(['id', 'business_name']);
            }
        ]);

        return view('app.services.service-report', compact('service'));
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
            ->where('is_dining_service', false)
            ->first(fields_get('services'));

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
                $query->select(fields_dotted('vouchers'))
                    ->whereBetween('vouchers.created_at', [
                        Carbon::parse($request->start)->startOfDay(),
                        Carbon::parse($request->end)->endOfDay()
                    ])
                    ->orderBy('vouchers.created_at', 'DESC')
                    ->withPivot('quantity', 'value');
            },
            'vouchers.company' => function ($query) use ($request)
            {
                $query->select(fields_dotted('companies'));
            },
        ]);

        if ($service->vouchers->isEmpty()) {
            flash(trans('common.without.results'))->info();

            return redirect()->route('services.service.report', ['id' => id_encode($service->id)]);
        }

        return Excel::download(new ServiceReport($service), trans('services.service') . '.xlsx');
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

            return redirect()->route('services.index');
        }

        return view('app.services.report', compact('hotels'));
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
                $query->select(fields_get('services'))
                    ->where('is_dining_service', false);
            },
            'services.vouchers' => function ($query) use ($request)
            {
                $query->select(fields_dotted('vouchers'))
                    ->whereBetween('vouchers.created_at', [
                        Carbon::parse($request->start)->startOfDay(),
                        Carbon::parse($request->end)->endOfDay()
                    ])
                    ->orderBy('vouchers.created_at', 'DESC')
                    ->withPivot('quantity', 'value');
            },
            'services.vouchers.company' => function ($query) use ($request)
            {
                $query->select(fields_dotted('companies'));
            }
        ]);

        $hotels = $query->get(fields_get('hotels'));

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return back();
        }

        return Excel::download(new ServicesReport($hotels), trans('services.title') . '.xlsx');
    }
}
