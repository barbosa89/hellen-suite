<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Hotel;
use App\Helpers\Chart;
use App\Helpers\Random;
use App\Models\Company;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Exports\ProductReport;
use Illuminate\Support\Carbon;
use App\Exports\ProductsReport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\{DateRangeQuery, ReportQuery, StoreProduct, UpdateProduct};

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hotels = Hotel::whereHas('owner', function (Builder $query) {
            $query->where('id', id_parent());
        })->with([
            'products' => function ($query) {
                $query->select(fields_get('products'));
            }
        ])->get(fields_get('hotels'));

        if ($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('hotels.index');
        }

        $hotels = $this->encodeIds($hotels);

        return view('app.products.index', compact('hotels'));
    }

    /**
     * Encode all ID's from collection
     *
     * @param  \Illuminate\Support\Collection
     * @return \Illuminate\Support\Collection
     */
    public function encodeIds(Collection $hotels)
    {
        $hotels = $hotels->map(function ($hotel) {
            $hotel->user_id = id_encode($hotel->user_id);
            $hotel->main_hotel = empty($hotel->main_hotel) ? null : id_encode($hotel->main_hotel);
            $hotel->products = $hotel->products->map(function ($product) {
                $product->hotel_id = id_encode($product->hotel_id);
                $product->user_id = id_encode($product->user_id);

                return $product;
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
        $hotels = Hotel::whereHas('owner', function (Builder $query) {
            $query->where('id', id_parent());
        })->whereStatus(true)
            ->get(fields_get('hotels'));

        if ($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return redirect()->route('hotels.index');
        }

        $companies = Company::where('user_id', id_parent())
            ->where('is_supplier', true)
            ->get(fields_get('companies'));

        return view('app.products.create', compact('hotels', 'companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProduct $request)
    {
        $product = new Product();
        $product->description = $request->description;
        $product->brand = $request->brand;
        $product->reference = $request->reference;
        $product->price = (float) $request->price;
        $product->quantity = $request->quantity;
        $product->user()->associate(id_parent());
        $product->hotel()->associate(id_decode($request->hotel));

        if ($product->save()) {
            // Voucher creation
            $voucher = new Voucher();
            $voucher->number = Random::consecutive();
            $voucher->open = false;
            $voucher->payment_status = true;
            $voucher->type = 'entry';
            $voucher->value = $product->price * $product->quantity;
            $voucher->subvalue = $product->price * $product->quantity;
            $voucher->made_by = auth()->user()->name;
            $voucher->comments = $request->comments;
            $voucher->hotel()->associate(id_decode($request->hotel));
            $voucher->user()->associate(id_parent());

            if (!empty($request->company)) {
                $voucher->company()->associate(id_decode($request->company));
            }

            if ($voucher->save()) {
                // Attach product
                $voucher->products()->attach(
                    $product->id,
                    [
                        'quantity' => $product->quantity,
                        'value' => $product->price * $product->quantity,
                        'created_at' => now()
                    ]
                );
            }

            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('products.show', [
                'id' => id_encode($product->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = User::find(id_parent(), ['id'])
            ->products()
            ->where('id', id_decode($id))
            ->first(fields_get('products'));

        if (empty($product)) {
            abort(404);
        }

        $product->load([
            'hotel' => function ($query) {
                $query->select(fields_get('hotels'));
            },
            'vouchers' => function ($query) {
                $query->select(fields_dotted('vouchers'))
                    ->orderBy('vouchers.created_at', 'DESC')
                    ->limit(20)
                    ->withPivot('quantity', 'value');
            }
        ]);

        $data = Chart::create($product->vouchers)
            ->countItems()
            ->get();

        return view('app.products.show', compact('product', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = User::find(id_parent(), ['id'])
            ->products()
            ->where('id', id_decode($id))
            ->with([
                'hotel' => function ($query) {
                    $query->select(fields_get('hotels'));
                }
            ])->first(fields_get('products'));

        if (empty($product)) {
            abort(404);
        }

        return view('app.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProduct $request, $id)
    {
        $product = User::find(id_parent(), ['id'])
            ->products()
            ->where('id', id_decode($id))
            ->first(fields_get('products'));

        if (empty($product)) {
            abort(404);
        }

        $product->description = $request->description;
        $product->brand = $request->brand;
        $product->reference = $request->reference;
        $product->price = (float) $request->price;

        if ($product->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('products.show', [
                'id' => id_encode($product->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = User::find(id_parent(), ['id'])->products()
            ->where('id', id_decode($id))
            ->first(fields_get('products'));

        if (empty($product)) {
            abort(404);
        }

        $product->load([
            'vouchers' => function ($query) {
                $query->select('vouchers.id');
            },
        ]);

        if ($product->vouchers->count() > 0) {
            $product->status = 0;

            if ($product->update()) {
                flash(trans('products.wasDisabled'))->success();

                return redirect()->route('products.index');
            }
        } else {
            if ($product->delete()) {
                flash(trans('common.deletedSuccessfully'))->success();

                return redirect()->route('products.index');
            }
        }

        flash(trans('common.error'))->error();

        return redirect()->route('products.index');
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
            $product = Product::find(id_decode($request->element), ['id', 'price']);

            if (empty($product)) {
                return response()->json(['value' => null]);
            } else {
                $value = (int) $request->quantity * $product->price;
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
        $product = User::find(id_parent(), ['id'])
            ->products()
            ->where('id', id_decode($id))
            ->first(fields_get('products'));

        if (empty($product)) {
            return abort(404);
        }

        $product->status = !$product->status;

        if ($product->save()) {
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

            $products = Product::where('hotel_id', id_decode($request->hotel))
                ->where('user_id', id_parent())
                ->whereLike(['description', 'brand', 'reference'], $query)
                ->get(fields_get('products'));

            $products = $products->map(function ($product) {
                $product->hotel_id = id_encode($product->hotel_id);
                $product->user_id = id_encode($product->user_id);

                return $product;
            });

            return response()->json([
                'products' => $products->toJson()
            ]);
        }

        abort(404);
    }

    /**
     * Display the product report form to query between dates.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showProductReportForm($id)
    {
        $product = User::find(id_parent(), ['id'])->products()
            ->where('id', id_decode($id))
            ->first(fields_get('products'));

        if (empty($product)) {
            abort(404);
        }

        $product->load([
            'hotel' => function ($query)
            {
                $query->select(['id', 'business_name']);
            }
        ]);

        return view('app.products.product-report', compact('product'));
    }

    /**
     * Export Product report in an excel document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function exportProductReport(DateRangeQuery $request, $id)
    {
        $product = User::find(id_parent(), ['id'])->products()
            ->where('id', id_decode($id))
            ->first(fields_get('products'));

        if (empty($product)) {
            abort(404);
        }

        $product->load([
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

        if ($product->vouchers->isEmpty()) {
            flash(trans('common.without.results'))->info();

            return redirect()->route('products.product.report', ['id' => id_encode($product->id)]);
        }

        return Excel::download(new ProductReport($product), trans('products.product') . '.xlsx');
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

            return redirect()->route('products.index');
        }

        return view('app.products.report', compact('hotels'));
    }

    /**
     * Export the products report in an excel document.
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
            'products' => function($query) {
                $query->select(fields_get('products'));
            },
            'products.vouchers' => function ($query) use ($request)
            {
                $query->select(fields_dotted('vouchers'))
                    ->whereBetween('vouchers.created_at', [
                        Carbon::parse($request->start)->startOfDay(),
                        Carbon::parse($request->end)->endOfDay()
                    ])
                    ->orderBy('vouchers.created_at', 'DESC')
                    ->withPivot('quantity', 'value');
            },
            'products.vouchers.company' => function ($query) use ($request)
            {
                $query->select(fields_dotted('companies'));
            }
        ]);

        $hotels = $query->get(fields_get('hotels'));

        if($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return back();
        }

        return Excel::download(new ProductsReport($hotels), trans('products.title') . '.xlsx');
    }
}
