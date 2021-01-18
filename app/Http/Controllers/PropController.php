<?php

namespace App\Http\Controllers;


use App\User;
use App\Models\Prop;
use App\Models\Hotel;
use App\Exports\PropReport;
use App\Exports\PropsReport;
use App\Helpers\Chart;
use App\Helpers\Random;
use App\Http\Requests\DateRangeQuery;
use App\Http\Requests\ReportQuery;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProp;
use App\Http\Requests\UpdateProp;
use App\Models\Company;
use App\Models\Voucher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class PropController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hotels = Hotel::where('user_id', id_parent())
            ->where('status', true)
            ->with([
                'props' => function ($query)
                {
                    $query->select(fields_get('props'));
                }
            ])->get(fields_get('hotels'));

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            if (auth()->user()->can('hotels.index')) {
                return redirect()->route('hotels.index');
            }

            return redirect()->route('home');
        }

        $hotels = $this->encodeIds($hotels);

        return view('app.props.index', compact('hotels'));
    }

    /**
     * Encode all ID's from collection
     *
     * @param  \Illuminate\Support\Collection
     * @return \Illuminate\Support\Collection
     */
    public function encodeIds(Collection $hotels)
    {
        $hotels = $hotels->map(function ($hotel)
        {
            $hotel->user_id = id_encode($hotel->user_id);
            $hotel->main_hotel = empty($hotel->main_hotel) ? null : id_encode($hotel->main_hotel);
            $hotel->props = $hotel->props->map(function ($prop)
            {
                $prop->hotel_id = id_encode($prop->hotel_id);
                $prop->user_id = id_encode($prop->user_id);

                return $prop;
            });

            return $hotel;
        });

        return $hotels;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hotels = Hotel::where('user_id', id_parent())
            ->where('status', true)
            ->get(fields_get('hotels'));

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('props.index');
        }

        $companies = Company::where('user_id', id_parent())
            ->where('is_supplier', true)
            ->get(fields_get('companies'));

        return view('app.props.create', compact('hotels', 'companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProp $request)
    {
        $prop = new Prop();
        $prop->description = $request->description;
        $prop->price = (float) $request->price;
        $prop->quantity = (int) $request->quantity;
        $prop->user()->associate(id_parent(), ['id']);
        $prop->hotel()->associate(id_decode($request->hotel));

        if ($prop->save()) {
            // Voucher creation
            $voucher = new Voucher();
            $voucher->number = Random::consecutive();
            $voucher->open = false;
            $voucher->payment_status = true;
            $voucher->type = 'entry';
            $voucher->value = $prop->price * $prop->quantity;
            $voucher->subvalue = $prop->price * $prop->quantity;
            $voucher->made_by = auth()->user()->name;
            $voucher->comments = $request->comments;
            $voucher->hotel()->associate(id_decode($request->hotel));
            $voucher->user()->associate(id_parent());

            if (!empty($request->company)) {
                $voucher->company()->associate(id_decode($request->company));
            }

            if ($voucher->save()) {
                // Attach prop
                $voucher->props()->attach(
                    $prop->id,
                    [
                        'quantity' => $prop->quantity,
                        'value' => $prop->price * $prop->quantity,
                        'created_at' => now()
                    ]
                );
            }

            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('props.show', [
                'id' => id_encode($prop->id)
            ]);
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
        $prop = User::find(id_parent(), ['id'])->props()
            ->where('id', id_decode($id))
            ->first(fields_get('props'));

        if (empty($prop)) {
            abort(404);
        }

        $prop->load([
            'hotel' => function ($query)
            {
                $query->select(['id', 'business_name']);
            },
            'vouchers' => function ($query)
            {
                $query->select(fields_dotted('vouchers'))
                    ->whereYear('vouchers.created_at', date('Y'))
                    ->orderBy('vouchers.created_at', 'DESC')
                    ->withPivot('quantity');
            }
        ]);

        $data = Chart::create($prop->vouchers)
            ->countItems()
            ->get();

        return view('app.props.show', compact('prop', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $prop = User::find(id_parent(), ['id'])->props()
            ->where('id', id_decode($id))
            ->with([
                'hotel' => function ($query)
                {
                    $query->select(['id', 'business_name']);
                }
            ])->first(fields_get('props'));

        if (empty($prop)) {
            abort(404);
        }

        return view('app.props.edit', compact('prop'));
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
        $prop = User::find(id_parent(), ['id'])->props()
            ->where('id', id_decode($id))
            ->where('hotel_id', id_decode($request->hotel))
            ->first(['id', 'description']);

        if (empty($prop)) {
            abort(404);
        }

        $prop->description = $request->description;
        $prop->price = (float) $request->price;

        if ($prop->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('props.show', [
                'id' => id_encode($prop->id)
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
        $prop = User::find(id_parent(), ['id'])->props()
            ->where('id', id_decode($id))
            ->first(['id']);

        if (empty($prop)) {
            abort(404);
        }

        if ($prop->delete()) {
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
            $query = param_clean($request->get('query', null));

            $props = Prop::where('hotel_id', id_decode($request->hotel))
                ->where('user_id', id_parent())
                ->whereLike('description', $query)
                ->get(fields_get('props'));

            $props = $props->map(function ($prop)
            {
                $prop->hotel_id = id_encode($prop->hotel_id);
                $prop->user_id = id_encode($prop->user_id);

                return $prop;
            });

            return response()->json([
                'results' => $props->toJson()
            ]);
        }

        abort(403);
    }

    /**
     * Display the prop report form to query between dates.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showPropReportForm($id)
    {
        $prop = User::find(id_parent(), ['id'])->props()
            ->where('id', id_decode($id))
            ->first(fields_get('props'));

        if (empty($prop)) {
            abort(404);
        }

        $prop->load([
            'hotel' => function ($query)
            {
                $query->select(['id', 'business_name']);
            }
        ]);

        return view('app.props.prop-report', compact('prop'));
    }

    /**
     * Export Prop report in an excel document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function exportPropReport(DateRangeQuery $request, $id)
    {
        $prop = User::find(id_parent(), ['id'])->props()
            ->where('id', id_decode($id))
            ->first(fields_get('props'));

        if (empty($prop)) {
            abort(404);
        }

        $prop->load([
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

        if ($prop->vouchers->isEmpty()) {
            flash(trans('common.without.results'))->info();

            return redirect()->route('props.prop.report', ['id' => id_encode($prop->id)]);
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
        $hotels = Hotel::where('user_id', id_parent())
            ->get(fields_get('hotels'));

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('props.index');
        }

        return view('app.props.report', compact('hotels'));
    }

    /**
     * Export the props report in an excel document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportReport(ReportQuery $request)
    {
        $props = Prop::where('user_id', id_parent())
            ->when($request->hotel, function ($query) use ($request) {
                $query->where('id', id_decode($request->hotel));
            })
            ->with([
                'hotel' => function ($query)
                {
                    $query->select(fields_dotted('hotels'));
                },
                'vouchers' => function ($query) use ($request)
                {
                    $query->whereBetween('vouchers.created_at', [
                            Carbon::parse($request->start)->startOfDay(),
                            Carbon::parse($request->end)->endOfDay()
                        ])
                        ->orderBy('vouchers.created_at', 'DESC')
                        ->withPivot('quantity', 'value');
                },
                'vouchers.company' => function ($query)
                {
                    $query->select(fields_dotted('companies'));
                }
            ])
            ->get();

        return Excel::download(new PropsReport($props), trans('props.title') . '.xlsx');
    }

    // /**
    //  * Show the form for props replication between hotels.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function showFormToReplicate()
    // {
    //     $hotels = Hotel::where('user_id', id_parent(), ['id'])
    //         ->where('status', true)
    //         ->get(fields_get('hotels'));

    //     if($hotels->isEmpty()) {
    //         flash(trans('hotels.no.registered'))->info();

    //         return redirect()->route('props.index');
    //     }
    //     return view('app.props.replicate', compact('hotels'));
    // }

    // public function replicants(Replicate $request)
    // {
    //     $count = Prop::where('user_id', id_parent())
    //         ->where('hotel_id', id_decode($request->from))
    //         ->count();

    //     if ($count > 0) {
    //         return redirect()->route('props.replicate.items', [
    //             'from' => $request->from,
    //             'to' => $request->to
    //         ]);
    //     }

    //     flash('El hotel desde donde intenta replicar no tiene registros')->info();

    //     return back();
    // }

    // public function showFormWithItems($from, $to)
    // {
    //     $fromHotel = Hotel::where('user_id', id_parent())
    //         ->where('id', id_decode($from))
    //         ->with([
    //             'props' => function ($query)
    //             {
    //                 $query->select(fields_get('props'));
    //             }
    //         ])->first(fields_get('hotels'));

    //     $toHotel = Hotel::where('user_id', id_parent())
    //         ->where('id', id_decode($to))
    //         ->with([
    //             'props' => function ($query)
    //             {
    //                 $query->select(fields_get('props'));
    //             }
    //         ])->first(fields_get('hotels'));
    //     $diff = $fromHotel->props->pluck('description');
    //     dd($fromHotel, $toHotel, $diff);
    // }

    /**
     * Return a rooms list by hotel ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function replicate(Replicate $request)
    // {
    //     // $props = Prop::where('user_id', id_parent())
    //     //     ->where('hotel_id', id_decode($request->from))
    //     //     ->get(fields_get('props'));

    //     // $replicas = collect();
    //     // $props->each(function ($prop) use (&$replicas, $request)
    //     // {
    //     //     $exists = Prop::where('description', $prop->description)
    //     //         ->where('user_id', id_parent())
    //     //         ->where('hotel_id', id_decode($request->to))
    //     //         ->first(['id']);

    //     //     if (empty($exists)) {
    //     //         $replicas->push([
    //     //             'description' => $prop->description,
    //     //             'hotel_id' => id_decode($request->to),
    //     //             'user_id' => id_parent()
    //     //         ]);
    //     //     }
    //     // });

    //     // $result = Prop::insert($replicas->toArray());
    //     // dd($result);
    // }
}
