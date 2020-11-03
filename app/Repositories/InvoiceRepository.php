<?php

namespace App\Repositories;

use App\Models\Plan;
use App\Helpers\Random;
use App\Models\Invoice;
use App\Models\Currency;
use App\Contracts\Repository;
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
    public function findById(int $id): Invoice
    {
        return Invoice::findOrFail('id', $id);
    }

    /**
     * Get paginated model collection
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Invoice::paginate($perPage);
    }

    /**
     * Get complete model collection
     *
     * @param int $perPage
     * @return \Illuminate\Support\Collection
     */
    public function all(array $filters = []): Collection
    {
        return Invoice::all();
    }

    /**
     * Create new Note
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
        $invoice = $this->findById($id);
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
        $invoice = $this->findById($id);

        return $invoice->delete();
    }
}
