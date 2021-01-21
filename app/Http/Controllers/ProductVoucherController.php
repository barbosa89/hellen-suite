<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Shift;
use App\Helpers\Random;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductVoucherController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hotels = $this->getHotels();

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('products.index');
        }

        $companies = Company::where('user_id', id_parent())
            ->where('is_supplier', true)
            ->get(fields_get('companies'));

        return view('app.products.vouchers.create', compact('hotels', 'companies'));
    }

    /**
     * Return the hotel list
     *
     * @return  \Illuminate\Support\Collection
     */
    private function getHotels()
    {
        if (auth()->user()->hasRole('receptionist')) {
            $user = auth()->user()->load([
                'headquarters' => function ($query)
                {
                    $query->select(fields_dotted('hotels'))
                        ->where('status', true);
                }
            ]);

            return $user->headquarters;
        }

        $hotels = User::find(id_parent(), ['id'])
            ->hotels()
            ->where('status', true)
            ->get(fields_get('hotels'));

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
        $products = $this->getProducts($request, $ids);
        $processed = collect();

        DB::transaction(function () use (&$processed, $request, $products) {
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
                    // Get the product to process
                    $product = $products->where('id', id_decode($element['hash']))->first();

                    // If voucher type is an entry,
                    // add the quantity and change to new price
                    // else, subtract the quantity
                    if ($request->type == 'entry') {
                        $product->quantity += $element['amount'];
                        $product->price = $element['price'];
                    } else {
                        $product->quantity -= $element['amount'];
                    }

                    // Calculations
                    $value = $product->price * $element['amount'];

                    // Prepare attach by product
                    $attach[$product->id] = [
                        'quantity' => $element['amount'],
                        'value' => $product->price * $element['amount'],
                        'created_at' => now()
                    ];

                    // On product save, add values to voucher
                    if ($product->save()) {
                        $voucher->subvalue += $value;
                        $voucher->value += $value;

                        // Push as product processed
                        $processed->push(id_encode($product->id));
                    }
                }

                // On voucher save, attach the pivot values
                if ($voucher->save()) {
                    $voucher->products()->sync($attach);

                    // Get the shift
                    $shift = Shift::current(id_decode($request->hotel));

                    // Check if user is the shift owner
                    // Else, the sale was done by admin people
                    // The generated voucher is not added to the shift
                    if ($shift->team_member == auth()->user()->id) {
                        $voucher->shifts()->attach($shift);

                        // Only if the voucher is sale type, the value is loaded to the shift
                        if ($voucher->type == 'sale') {
                            $shift->cash += $voucher->value;

                            if ($shift->save()) {
                                // Add new automatic payment
                                $payment = new Payment();
                                $payment->date = now();
                                $payment->commentary = trans('payments.automatic');
                                $payment->payment_method = 'cash';
                                $payment->value = $voucher->value;
                                $payment->voucher()->associate($voucher->id);
                                $payment->save();
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::error(trans('common.error'), [
                    'file' => $e->getFile(),
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                ]);
            }
        });

        return response()->json([
            'processed' => $processed->toArray()
        ]);
    }

    /**
     * Return a products collections.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $ids
     * @return \Illuminate\Support\Collection
     */
    public function getProducts(Request $request, array $ids)
    {
        $products = Product::where('user_id', id_parent())
            ->where('hotel_id', id_decode($request->hotel))
            ->whereIn('id', id_decode_recursive($ids))
            ->where('status', true)
            ->get(fields_get('products'));

        return $products;
    }
}
