<?php

namespace App\Http\Controllers;

use App\User;
use App\Helpers\Id;
use App\Welkome\Prop;
use App\Welkome\Hotel;
use App\Helpers\Fields;
use App\Helpers\Input;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProp;
use App\Http\Requests\UpdateProp;
use App\Welkome\Transaction;
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
        $prop = new Prop();
        $prop->description = $request->description;
        $prop->quantity = $request->quantity;
        $prop->user()->associate(Id::parent(), ['id']);
        $prop->hotel()->associate(Id::get($request->hotel));

        if ($prop->save()) {
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
            'transactions' => function ($query)
            {
                $query->select(['id', 'amount', 'commentary', 'type', 'done_by', 'transactionable_id']);
            }
        ]);

        return view('app.props.show', compact('prop'));
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
                    $query->select(['id']);
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
                'props' => $props->toJson()
            ]);
        }

        abort(404);
    }

    /**
     * Show the form for prop inventory inputs and outputs.
     *
     * @return \Illuminate\Http\Response
     */
    public function showTransactionsForm()
    {
        $hotels = Hotel::where('user_id', Id::parent(), ['id'])
            ->where('status', true)
            ->get(['id', 'business_name']);

        if($hotels->isEmpty()) {
            flash('No hay hoteles creados')->info();

            return redirect()->route('props.index');
        }

        return view('app.props.transactions', compact('hotels'));
    }

    /**
     * Process Props transactions inputs/outputs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function transactions(Request $request)
    {
        $props = collect($request->props);
        $count = 0;

        $props->each(function ($item, $index) use (&$count, $request) {
            $prop = Prop::where('user_id', Id::parent())
                ->where('id', Id::get($item['hash']))
                ->where('hotel_id', Id::get($request->hotel))
                ->first(['id', 'quantity']);

            if (!empty($prop)) {
                $transaction = new Transaction();
                $transaction->amount = (int) $item['amount'];
                $transaction->commentary = $item['commentary'];
                $transaction->type = $request->type;
                $transaction->done_by = substr(auth()->user()->name, 0, 100);
                $transaction->user()->associate(Id::parent());

                if ($prop->transactions()->save($transaction)) {
                    if ($request->type == 'input') {
                        $prop->quantity += (int) $item['amount'];
                    }

                    if ($request->type == 'output') {
                        $prop->quantity -= (int) $item['amount'];
                    }

                    if ($prop->save()) {
                        $count++;
                    } else {
                        $transaction->delete();
                    }
                }
            }
        });

        return response()->json([
            'request' => $props->count(),
            'processed' => $count
        ]);

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
