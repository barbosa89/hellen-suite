<?php

namespace App\Http\Controllers;

use App\User;
use App\Helpers\Id;
use App\Welkome\Service;
use Illuminate\Http\Request;
use App\Http\Requests\{StoreService, UpdateService};

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = User::find(Id::parent(), ['id'])->services()
            ->paginate(config('welkome.paginate'), [
                'id', 'description', 'price', 'status', 'user_id', 'created_at'
            ])->sortByDesc('created_at');

        return view('app.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app.services.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreService $request)
    {
        $service = new service();
        $service->description = $request->description;
        $service->price = (float) $request->price;
        $service->user()->associate(auth()->user()->id);

        if ($service->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('services.index');
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
        $service = User::find(Id::parent(), ['id'])->services()
            ->where('id', Id::get($id))
            ->first([
                'id', 'description', 'price', 'user_id', 'created_at'
            ]);

        if (empty($service)) {
            abort(404);
        }

        return view('app.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = User::find(Id::parent(), ['id'])->services()
            ->where('id', Id::get($id))
            ->first([
                'id', 'description', 'price', 'user_id', 'created_at'
            ]);

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
        $service = User::find(Id::parent(), ['id'])->services()
            ->where('id', Id::get($id))
            ->first([
                'id', 'description', 'price', 'user_id',
            ]);

        if (empty($service)) {
            abort(404);
        }

        $service->description = $request->description;
        $service->price = (float) $request->price;

        if ($service->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('services.index');
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
        $service = User::find(Id::parent(), ['id'])->services()
            ->where('id', Id::get($id))
            ->first([
                'id', 'description', 'price', 'user_id', 'created_at'
            ]);

        if (empty($service)) {
            abort(404);
        }

        $service->load([
            'invoices' => function ($query)
            {
                $query->select('id');
            },
        ]);

        if ($service->invoices->count() > 0) {
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
     * Increase service existence or quantity.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showIncreaseForm($id)
    {
        $service = User::find(Id::parent(), ['id'])->services()
            ->where('id', Id::get($id))
            ->first([
                'id', 'description', 'price', 'user_id', 'created_at'
            ]);

        if (empty($service)) {
            abort(404);
        }

        return view('app.services.increase', compact('service'));
    }

    /**
     * Increase the service's quantity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function increase(Increaseservice $request, $id)
    // {
    //     $service = User::find(Id::parent(), ['id'])->services()
    //         ->where('id', Id::get($id))
    //         ->first([
    //             'id', 'description', 'price', 'user_id', 'created_at'
    //         ]);

    //     if (empty($service)) {
    //         abort(404);
    //     }

    //     $service->quantity += $request->quantity;

    //     if ($service->update()) {
    //         flash(trans('common.updatedSuccessfully'))->success();

    //         return redirect()->route('services.index');
    //     }

    //     flash(trans('common.error'))->error();

    //     return redirect()->route('services.index');
    // }

    /**
     * Return price of resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function total(Request $request)
    {
        if ($request->ajax()) {
            $service = Service::find(Id::get($request->element), ['id', 'price']);

            if (empty($service)) {
                return response()->json(['value' => null]);
            } else {
                $value = (int) $request->quantity * $service->price;
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
        $service = User::find(Id::parent(), ['id'])->services()
            ->where('id', Id::get($id))
            ->first([
                'id', 'description', 'price', 'status', 'user_id', 'created_at'
            ]);

        if (empty($service)) {
            return abort(404);
        }

        $service->status = !$service->status;

        if ($service->save()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect(url()->previous());
        }

        flash(trans('common.error'))->error();

        return redirect(url()->previous());
    }
}
