<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePlan;
use App\Models\Currency;
use App\Models\IdentificationType;
use App\Models\Plan;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::all(['id', 'price', 'months', 'type', 'status']);

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
        $plan = Plan::findOrFail(id_decode($id), ['id', 'price', 'months', 'type', 'status']);

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
        $plan = Plan::findOrFail(id_decode($id), ['id', 'price', 'months', 'type', 'status']);

        $plan->fill($request->validated());

        if ($plan->save()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('plans.index');
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * View to choose the plan
     *
     * @return \Illuminate\Http\Response
     */
    public function choose()
    {
        // All active plans
        $plans = Plan::active()
            ->notRelatedToUser()
            ->get(['id', 'price', 'months', 'type', 'status']);

        if ($plans->isEmpty()) {
            return redirect()->route('home');
        }

        return view('app.plans.choose', compact('plans'));
    }

    /**
     * Undocumented function
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function buy(string $id)
    {
        $plan = Plan::active()
            ->notRelatedToUser()
            ->where('id', id_decode($id))
            ->firstOrFail(['id', 'price', 'months', 'type', 'status']);

        if ($plan->type == Plan::FREE) {
            auth()
                ->user()
                ->plans()
                ->attach($plan, ['ends_at' => now()->addMonth()]);

            $type = trans('plans.type.' . $plan->getType());

            flash(trans('plans.ready', ['plan' => $type]))->success();

            return redirect()->route('home');
        }

        $types = IdentificationType::all(['id', 'type']);
        $currencies = Currency::all(['id', 'code']);

        return view('app.plans.buy', compact('plan', 'types', 'currencies'));
    }

    public function renew()
    {
        # code...
    }
}
