<?php

namespace App\Http\Controllers;


use App\User;
use App\Helpers\Id;
use App\Welkome\Prop;
use App\Welkome\Hotel;
use App\Helpers\Input;
use App\Helpers\Fields;
use App\Exports\PropReport;
use App\Exports\PropsReport;
use App\Helpers\Random;
use App\Http\Requests\PropReportQuery;
use App\Http\Requests\PropsReportQuery;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProp;
use App\Http\Requests\UpdateProp;
use App\Welkome\Company;
use App\Welkome\Voucher;
use Illuminate\Support\Collection;
use Vinkla\Hashids\Facades\Hashids;
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
        $hotels = Hotel::where('user_id', Id::parent())
            ->where('status', true)
            ->with([
                'props' => function ($query)
                {
                    $query->select(Fields::get('props'));
                }
            ])->get(Fields::get('hotels'));

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

        return $hotels;
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
            ->get(Fields::get('hotels'));

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('props.index');
        }

        $companies = Company::where('user_id', Id::parent())
            ->where('is_supplier', true)
            ->get(Fields::get('companies'));

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
        $prop->user()->associate(Id::parent(), ['id']);
        $prop->hotel()->associate(Id::get($request->hotel));

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
            $voucher->hotel()->associate(Id::get($request->hotel));
            $voucher->user()->associate(Id::parent());

            if (!empty($request->company)) {
                $voucher->company()->associate(Id::get($request->company));
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
                'id' => Hashids::encode($prop->id)
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
            'vouchers' => function ($query)
            {
                $query->select(Fields::parsed('vouchers'))
                    ->whereYear('vouchers.created_at', date('Y'))
                    ->orderBy('vouchers.created_at', 'DESC')
                    ->withPivot('quantity');
            }
        ]);

        $types = $this->groupVoucherTypesByMonth($prop->vouchers);
        $data = $this->prepareChartData($types);

        return view('app.props.show', compact('prop', 'data'));
    }

    /**
     * Group transaction types by month.
     *
     * @param  \Illuminate\Support\Collection $transaction
     * @return \Illuminate\Support\Collection $types
     */
    public function groupVoucherTypesByMonth(Collection $vouchers)
    {
        $types = $vouchers->groupBy([
            function($voucher) {
                return $voucher->type;
            }, function ($voucher)
            {
                return $voucher->created_at->month;
            }
        ]);

        return $types;
    }

    /**
     * Prepare chart data by voucher type in a yearly period.
     *
     * @param  \Illuminate\Support\Collection $types
     * @return array $data
     */
    public function prepareChartData(Collection $types)
    {
        $types = $types->toArray();
        $data = [];

        foreach ($types as $type => $months) {
            foreach ($months as $month => $vouchers) {
                foreach ($vouchers as $voucher) {
                    if (isset($types[$type][$month])) {
                        $data[$type][$month] = $voucher['pivot']['quantity'];
                    } else {
                        $data[$type][$month] = 0;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $prop = User::find(Id::parent(), ['id'])->props()
            ->where('id', Id::get($id))
            ->with([
                'hotel' => function ($query)
                {
                    $query->select(['id', 'business_name']);
                }
            ])->first(Fields::get('props'));

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
        $prop = User::find(Id::parent(), ['id'])->props()
            ->where('id', Id::get($id))
            ->where('hotel_id', Id::get($request->hotel))
            ->first(['id', 'description']);

        if (empty($prop)) {
            abort(404);
        }

        $prop->description = $request->description;
        $prop->price = (float) $request->price;

        if ($prop->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('props.show', [
                'id' => Hashids::encode($prop->id)
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
        $prop = User::find(Id::parent(), ['id'])->props()
            ->where('id', Id::get($id))
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
            $query = Input::clean($request->get('query', null));

            $props = Prop::where('hotel_id', Id::get($request->hotel))
                ->where('user_id', Id::parent())
                ->whereLike('description', $query)
                ->get(Fields::get('props'));

            $props = $props->map(function ($prop)
            {
                $prop->hotel_id = Hashids::encode($prop->hotel_id);
                $prop->user_id = Hashids::encode($prop->user_id);

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
    public function exportPropReport(PropReportQuery $request, $id)
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

        if ($prop->vouchers->isEmpty()) {
            flash(trans('common.without.results'))->info();

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
    public function exportReport(PropsReportQuery $request)
    {
        if (empty($request->get('hotel', null))) {
            $hotels = Hotel::where('user_id', Id::parent())
                ->with([
                    'props' => function($query) {
                        $query->select(Fields::get('props'));
                    },
                    'props.transactions' => function ($query) use ($request)
                    {
                        $query->select(Fields::get('transactions'))
                            ->whereBetween('created_at', [$request->start, $request->end])
                            ->orderBy('created_at', 'DESC');
                    }
                ])->get(Fields::get('hotels'));
        } else {
            $hotels = Hotel::where('user_id', Id::parent())
                ->where('id', Id::get($request->hotel))
                ->with([
                    'props' => function($query) {
                        $query->select(Fields::get('props'));
                    },
                    'props.transactions' => function ($query) use ($request)
                    {
                        $query->select(Fields::get('transactions'))
                            ->whereBetween('created_at', [$request->start, $request->end])
                            ->orderBy('created_at', 'DESC');
                    }
                ])->get(Fields::get('hotels'));
        }

        if($hotels->isEmpty()) {
            flash('No hay hoteles creados')->info();

            return back();
        }

        return Excel::download(new PropsReport($hotels), trans('props.title') . '.xlsx');
    }

    // /**
    //  * Show the form for props replication between hotels.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function showFormToReplicate()
    // {
    //     $hotels = Hotel::where('user_id', Id::parent(), ['id'])
    //         ->where('status', true)
    //         ->get(Fields::get('hotels'));

    //     if($hotels->isEmpty()) {
    //         flash('No hay hoteles creados')->info();

    //         return redirect()->route('props.index');
    //     }
    //     return view('app.props.replicate', compact('hotels'));
    // }

    // public function replicants(Replicate $request)
    // {
    //     $count = Prop::where('user_id', Id::parent())
    //         ->where('hotel_id', Id::get($request->from))
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
    //     $fromHotel = Hotel::where('user_id', Id::parent())
    //         ->where('id', Id::get($from))
    //         ->with([
    //             'props' => function ($query)
    //             {
    //                 $query->select(Fields::get('props'));
    //             }
    //         ])->first(Fields::get('hotels'));

    //     $toHotel = Hotel::where('user_id', Id::parent())
    //         ->where('id', Id::get($to))
    //         ->with([
    //             'props' => function ($query)
    //             {
    //                 $query->select(Fields::get('props'));
    //             }
    //         ])->first(Fields::get('hotels'));
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
    //     // $props = Prop::where('user_id', Id::parent())
    //     //     ->where('hotel_id', Id::get($request->from))
    //     //     ->get(Fields::get('props'));

    //     // $replicas = collect();
    //     // $props->each(function ($prop) use (&$replicas, $request)
    //     // {
    //     //     $exists = Prop::where('description', $prop->description)
    //     //         ->where('user_id', Id::parent())
    //     //         ->where('hotel_id', Id::get($request->to))
    //     //         ->first(['id']);

    //     //     if (empty($exists)) {
    //     //         $replicas->push([
    //     //             'description' => $prop->description,
    //     //             'hotel_id' => Id::get($request->to),
    //     //             'user_id' => Id::parent()
    //     //         ]);
    //     //     }
    //     // });

    //     // $result = Prop::insert($replicas->toArray());
    //     // dd($result);
    // }
}
