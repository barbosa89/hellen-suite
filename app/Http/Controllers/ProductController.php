<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use App\Helpers\Id;
use App\Welkome\Product;
use Illuminate\Http\Request;
use App\Http\Requests\{StoreProduct, UpdateProduct, IncreaseProduct};

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = User::find(auth()->user()->id)->products()
            ->paginate(config('welkome.paginate'), [
                'id', 'description', 'brand', 'reference', 'price', 'user_id', 'quantity'
            ])->sort();

        return view('app.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app.products.create');
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
        $product->user()->associate(auth()->user()->id);

        if ($product->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('products.index');
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
        $product = User::find(auth()->user()->id)->products()
            ->where('id', Id::get($id))
            ->first([
                'id', 'description', 'brand', 'reference', 'price', 'user_id', 'quantity'
            ]);

        if (empty($product)) {
            abort(404);
        }

        return view('app.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = User::find(auth()->user()->id)->products()
            ->where('id', Id::get($id))
            ->first([
                'id', 'description', 'brand', 'reference', 'price', 'user_id', 'quantity'
            ]);

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
        $product = User::find(auth()->user()->id)->products()
            ->where('id', Id::get($id))
            ->first([
                'id', 'description', 'brand', 'reference', 'price', 'user_id',
            ]);

        if (empty($product)) {
            abort(404);
        }

        $product->description = $request->description;
        $product->brand = $request->brand;
        $product->reference = $request->reference;
        $product->price = (float) $request->price;

        if ($product->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('products.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = User::find(auth()->user()->id)->products()
            ->where('id', Id::get($id))
            ->first([
                'id', 'description', 'brand', 'reference', 'price', 'user_id', 'quantity'
            ]);

        if (empty($product)) {
            abort(404);
        }

        $product->load([
            'invoices' => function ($query)
            {
                $query->select('id');
            },
        ]);

        if ($product->invoices->count() > 0) {
            $now = Carbon::now()->toDateTimeString();
            $description = $product->description . ' (' . trans('common.disabled') . '-' . $now . ')';

            $product->description = $description;
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
     * Increase product existence or quantity.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showIncreaseForm($id)
    {
        $product = User::find(auth()->user()->id)->products()
            ->where('id', Id::get($id))
            ->first([
                'id', 'description', 'brand', 'reference', 'price', 'user_id', 'quantity'
            ]);

        if (empty($product)) {
            abort(404);
        }

        return view('app.products.increase', compact('product'));
    }

    /**
     * Increase the product's quantity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function increase(IncreaseProduct $request, $id)
    {
        $product = User::find(auth()->user()->id)->products()
            ->where('id', Id::get($id))
            ->first([
                'id', 'description', 'brand', 'reference', 'price', 'user_id', 'quantity'
            ]);

        if (empty($product)) {
            abort(404);
        }

        $product->quantity += $request->quantity;

        if ($product->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('products.index');
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
            $product = Product::find(Id::get($request->element), ['id', 'price']);

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
}
