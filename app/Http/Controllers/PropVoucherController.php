<?php

namespace App\Http\Controllers;

use App\Helpers\Random;
use App\Models\Company;
use App\Models\Hotel;
use App\Models\Prop;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PropVoucherController extends Controller
{
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

            if (auth()->user()->can('hotels.index')) {
                return redirect()->route('hotels.index');
            }

            return redirect()->route('home');
        }

        $companies = Company::where('user_id', id_parent())
            ->where('is_supplier', true)
            ->get(fields_get('companies'));

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
                $voucher->hotel()->associate(id_decode($request->hotel));
                $voucher->user()->associate(id_parent());

                // Check if a supplier was selected
                if (!empty($request->company)) {
                    $voucher->company()->associate(id_decode($request->company));
                }

                foreach ($request->elements as $element) {
                    // Get the prop to process
                    $prop = $props->where('id', id_decode($element['hash']))->first();

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
                        $processed->push(id_encode($prop->id));
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

    /**
     * Return a props collections.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $ids
     * @return \Illuminate\Support\Collection
     */
    public function getProps(Request $request, array $ids)
    {
        $props = Prop::where('user_id', id_parent())
            ->where('hotel_id', id_decode($request->hotel))
            ->whereIn('id', id_decode_recursive($ids))
            ->where('status', true)
            ->get(fields_get('props'));

        return $props;
    }
}
