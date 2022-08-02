<?php

namespace App\Repositories;

use stdClass;
use App\Models\Plan;
use App\Helpers\Random;
use App\Models\Invoice;
use App\Models\Currency;
use App\Contracts\Repository;
use App\Models\InvoicePayment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Pure Eloquent Repository
 */
class InvoiceRepository implements Repository
{
    /**
     * Get model
     *
     * @param int $id
     * @return \App\Models\Invoice
     */
    public function find(int $id): Invoice
    {
        return Invoice::whereOwner()
            ->where('id', $id)
            ->with([
                'currency' => function ($query)
                {
                    $query->select(['id', 'code']);
                },
                'identificationType' => function ($query)
                {
                    $query->select(['id', 'type']);
                },
                'plans' => function ($query)
                {
                    $query->select(['plans.id', 'plans.price', 'plans.months', 'plans.type']);
                },
                'payments' => function ($query)
                {
                    $query->select(['id', 'number', 'value', 'payment_method', 'status', 'invoice_id', 'created_at']);
                }
            ])
            ->selectAll()
            ->firstOrFail();
    }

    /**
     * Get paginated model collection
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Invoice::whereOwner()
            ->latest()
            ->with([
                'currency' => function ($query)
                {
                    $query->select(['id', 'code']);
                },
                'identificationType' => function ($query)
                {
                    $query->select(['id', 'type']);
                }
            ])
            ->selectAll()
            ->paginate($perPage);
    }

    /**
     * Get complete model collection
     *
     * @param int $perPage
     * @return \Illuminate\Support\Collection
     */
    public function all(array $filters = []): Collection
    {
        return Invoice::whereOwner()
            ->latest()
            ->selectAll()
            ->limit(100)
            ->get();
    }

    /**
     * Create new Invoice
     *
     * @param   array $data
     * @return  \App\Models\Invoice
     */
    public function create(array $data): Invoice
    {
        $plan = Plan::active()
            ->notRelatedToUser()
            ->where('id', $data['plan_id'])
            ->firstOrFail();

        $currency = Currency::findOrFail($data['currency_id'], ['id', 'code']);

        if ($currency->code == Currency::COP) {
            $value = $plan->price;
        }

        if ($currency->code == Currency::USD) {
            $value = $plan->getDollarPrice();
        }

        $data['number'] = Random::consecutive('invoices');
        $data['status'] = Invoice::PENDING;
        $data['value'] = $value;
        $data['total'] = $value;

        $invoice = new Invoice();
        $invoice->fill($data);
        $invoice->identificationType()->associate($data['type_id']);
        $invoice->currency()->associate($currency);
        $invoice->user()->associate(auth()->user());
        $invoice->saveOrFail();

        $invoice->plans()->attach($plan);

        return $invoice;
    }

    /**
     * Update model
     *
     * @param  integer $id
     * @param  array $data
     * @return \App\Models\Invoice
     */
    public function update(int $id, array $data): Invoice
    {
        $invoice = $this->find($id);
        $invoice->fill($data);
        $invoice->saveOrFail();

        return $invoice;
    }

    /**
     * Destroy model
     *
     * @param integer $id
     * @return boolean
     */
    public function destroy(int $id): bool
    {
        $invoice = Invoice::whereOwner()
            ->where('id', $id)
            ->where('status', '!=', Invoice::PAID)
            ->selectAll()
            ->first();

        if (empty($invoice)) {
            return false;
        }

        return $invoice->delete();
    }

    /**
     * Get all pending invoices related to a plan
     *
     * @param integer $planId
     * @return \Illuminate\Support\Collection
     */
    public function pendingWithPlan(int $planId): Collection
    {
        return Invoice::whereOwner()
            ->where('status', Invoice::PENDING)
            ->whereHas('plans', function ($query) use ($planId) {
                return $query->where('plans.id', $planId);
            })
            ->selectAll()
            ->get();
    }

    /**
     * Process an approved payment
     *
     * @param string $number
     * @param stdClass $data
     * @return \App\Models\Invoice
     */
    public function processPayment(string $number, stdClass $data): Invoice
    {
        $invoice = Invoice::whereOwner()
            ->where('number', $number)
            ->where('status', Invoice::PENDING)
            ->with([
                'plans' => function ($query)
                {
                    return $query->select(get_columns('plans', true));
                },
                'user' => function ($query)
                {
                    return $query->select(['users.id']);
                },
                'user.plans' => function ($query)
                {
                    return $query->select(get_columns('plans', true));
                }
            ])
            ->selectAll()
            ->firstOrFail();

        $payment = new InvoicePayment();
        $payment->number = $data->data->id;
        $payment->value = cents_to_float($data->data->amount_in_cents);
        $payment->payment_method = $data->data->payment_method_type;
        $payment->status = InvoicePayment::APPROVED;
        $payment->invoice()->associate($invoice);
        $payment->save();

        $invoice->status = Invoice::PAID;
        $invoice->save();

        $plan = $invoice->plans->first();

        // Check if the plan is attached to user
        if ($invoice->user->plans->contains('id', $plan->id)) {
            $plan = $invoice->user->plans->firstWhere('id', $plan->id);

            $ends_at = Carbon::parse($plan->pivot->ends_at);

            $plan->users()->updateExistingPivot($invoice->user->id, [
                'ends_at' => $ends_at->addMonths($plan->months)
            ]);
        } else {
            $plan->users()->attach($invoice->user->id, [
                'ends_at' => now()->addMonths($plan->months)
            ]);
        }

        return $invoice;
    }
}
