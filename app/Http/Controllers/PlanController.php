<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuyPlan;
use App\Http\Requests\UpdatePlan;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::allColumns()->get();

        return view('app.plans.index', compact('plans'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $plan = Plan::allColumns()
            ->where('id', id_decode($id))
            ->first();

        return view('app.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePlan  $request
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePlan $request, string $id)
    {
        $plan = Plan::allColumns()
            ->where('id', id_decode($id))
            ->first();

        $plan->fill($request->validated());

        if ($plan->save()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('plans.index');
        }

        flash(trans('common.error'))->error();

        return back();
    }

    public function choose()
    {
        // All active plans
        $plans = Plan::active()->get();

        return view('app.plans.choose', compact('plans'));
    }

    public function buy(BuyPlan $request)
    {
        dd($request->toArray());
        $plan = Plan::find($request->plan_id);

        auth()->user()->plans()->attatch($plan, [
            'ends_at' => now()->addMonths($plan->months)
        ]);

        // faltan los datos

        // Si es free sólo attach
        // si es basic crear invoice
    }

    public function renew()
    {
        # code...
    }
}
