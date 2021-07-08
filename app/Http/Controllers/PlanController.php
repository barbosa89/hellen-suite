<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Currency;
use App\Http\Requests\UpdatePlan;
use App\Models\IdentificationType;
use Illuminate\Support\Collection;

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
            ->get(['plans.id', 'plans.price', 'plans.months', 'plans.type', 'plans.status']);

        return view('app.plans.choose', compact('plans'));
    }

    /**
     * Form to buy a plan.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function buy(string $id)
    {
        $plan = Plan::active()
            ->where('id', id_decode($id))
            ->firstOrFail(['id', 'price', 'months', 'type', 'status']);

        if ($plan->type == Plan::FREE) {
            auth()
                ->user()
                ->plans()
                ->attach($plan, ['ends_at' => now()->addMonths($plan->months)]);

            $type = trans('plans.type.' . $plan->getType());

            flash(trans('plans.ready', ['plan' => $type]))->success();

            return redirect()->route('home');
        }

        $types = IdentificationType::all(['id', 'type']);
        $currencies = Currency::all(['id', 'code']);

        return view('app.plans.buy', compact('plan', 'types', 'currencies'));
    }

    /**
     * Form to renew a plan.
     *
     * @return \Illuminate\Http\Response
     */
    public function renew()
    {
        $user = auth()->user()->load('plans:id,price,months,type,status');

        // If user has active plans
        if ($this->hasActivePlans($user->plans)) {
            return redirect()->route('home');
        }

        // If the user plans are empty or the unique plan is the free plan
        // the user must select a paid plan
        if ($user->plans->isEmpty() or $this->hasFreeExpiredPlan($user->plans)) {
            return redirect()->route('plans.choose');
        }

        $plans = Plan::active()
            ->nonFree()
            ->get(['id', 'price', 'months', 'type', 'status']);

        return view('app.plans.choose', compact('plans'));
    }

    /**
     * Check if user has active plans.
     *
     * @param \Illuminate\Support\Collection $plans
     * @return boolean
     */
    private function hasActivePlans(Collection $plans): bool
    {
        $actives = $plans->filter(function ($plan) {
            return $plan->isActive();
        });

        return $actives->isNotEmpty();
    }

    /**
     * Check if user has free expired plan.
     *
     * @param \Illuminate\Support\Collection $plans
     * @return boolean
     */
    private function hasFreeExpiredPlan(Collection $plans): bool
    {
        return $plans->count() == 1 and $plans->first()->type == Plan::FREE;
    }
}
