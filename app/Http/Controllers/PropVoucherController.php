<?php

namespace App\Http\Controllers;

use App\Helpers\Fields;
use App\Helpers\Id;
use App\Helpers\Random;
use App\Welkome\Company;
use App\Welkome\Hotel;
use App\Welkome\Prop;
use App\Welkome\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

class PropVoucherController extends Controller
{
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

            if (auth()->user()->can('hotels.index')) {
                return redirect()->route('hotels.index');
            }

            return redirect()->route('home');
        }

        $companies = Company::where('user_id', Id::parent())
            ->where('is_supplier', true)
            ->get(Fields::get('companies'));

        $hotels = $this->encodeIds($hotels);

        return view('app.props.vouchers.create', compact('hotels', 'companies'));
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ids = collect($request->elements)->pluck('hash')->toArray();
        $props = $this->getProps($request, $ids);
        $processed = collect();

        DB::transaction(function () use (&$processed, $request, $props) {
            try {
                // Voucher creation
                $voucher = new Voucher();
                $voucher->number = Random::consecutive();
                $voucher->open = false;
                $voucher->payment_status = true;
                $voucher->type = $request->type;
                $voucher->made_by = auth()->user()->name;
                $voucher->comments = $request->comments;
                $voucher->hotel()->associate(Id::get($request->hotel));
                $voucher->user()->associate(Id::parent());

                // Check if a supplier was selected
                if (!empty($request->company)) {
                    $voucher->company()->associate(Id::get($request->company));
                }

                foreach ($request->elements as $element) {
                    // Get the prop to process
                    $prop = $props->where('id', Id::get($element['hash']))->first();

                    // Calculations
                    $value = $prop->price * $element['amount'];

                    // Prepare attach by prop
                    $attach[$prop->id] = [
                        'quantity' => $element['amount'],
                        'value' => $prop->price * $element['amount'],
                        'created_at' => now()
                    ];

                    // If voucher type is an entry,
                    // add the quantity and change price in average
                    // else, subtract the quantity
                    if ($request->type == 'entry') {
                        $prop->quantity += $element['amount'];
                        $prop->price = ($prop->price / $element['price']) / 2;
                    } else {
                        $prop->quantity -= $element['amount'];
                    }

                    // On prop save, add values to voucher
                    if ($prop->save()) {
                        $voucher->subvalue += $value;
                        $voucher->value += $value;

                        // Push as Prop processed
                        $processed->push(Hashids::encode($prop->id));
                    }
                }

                // On voucher save, attach the pivot values
                if ($voucher->save()) {
                    $voucher->props()->sync($attach);
                }
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage() . ' Line: ' . $e->getLine());
            }
        });

        return response()->json([
            'processed' => $processed->toArray()
        ]);
    }

    public function getProps(Request $request, array $ids)
    {
        $props = Prop::where('user_id', Id::parent())
            ->where('hotel_id', Id::get($request->hotel))
            ->whereIn('id', Id::pool($ids))
            ->where('status', true)
            ->get(Fields::get('props'));

        return $props;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
