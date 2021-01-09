<?php

namespace App\Repositories;

use App\Repositories\Repository;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Pure Eloquent Repository
 */
class VoucherRepository implements Repository
{
    private Builder $model;

    /**
     * Create new Note
     *
     * @param   array $data
     * @return  \App\Models\Voucher
     */
    public function create(array $data): Voucher
    {
        return Voucher::create($data);
    }

    /**
     * Retrieve model by ID
     *
     * @param  integer $id
     * @return \App\Models\Voucher
     */
    public function get(int $id): Voucher
    {
        return Voucher::findOrFail($id);
    }

    /**
     * Update model
     *
     * @param  integer $id
     * @param  array $data
     * @return \App\Models\Voucher
     */
    public function update(int $id, array $data): Voucher
    {
        $voucher = $this->get($id);
        $voucher->fill($data);
        $voucher->saveOrFail();

        return $voucher;
    }

    /**
     * Destroy model
     *
     * @param integer $id
     * @return boolean
     */
    public function destroy(int $id): bool
    {
        // $note = $this->get($id);

        // return $note->delete();
        return true;
    }

    public function query()
    {
        $this->model = Voucher::query();
        $this->model->where('user_id', id_parent())
            ->where('status', true);

        return $this;
    }

    public function builder()
    {
        return $this->model;
    }

    public function loader(array $relationships, int $id = null)
    {
        // Get required relationships in args
        $loads = $this->getRequiredRelationships($relationships, $id);

        $this->model->with($loads);

        return $this;
    }

    public function load(Model $model, $relationships, int $id = null): Model
    {
        $model->load($this->getRequiredRelationships($relationships, $id));

        return $model;
    }

    public function getRequiredRelationships(array $relationshipNames, int $id = null)
    {
        $relationships = $this->getRelationships($id);
        $loads = [];

        foreach ($relationshipNames as $relationship) {
            if (key_exists($relationship, $relationships)) {
                $loads[$relationship] = $relationships[$relationship];
            } else {
                throw new InvalidArgumentException("Relationship not exists: {$relationship}", 1);
            }
        }

        return $loads;
    }

    private function getRelationships(int $id = null): array
    {
        return [
            'hotel' => function ($query) {
                $query->select(fields_get('hotels'));
            },
            'guests' => function ($query) use ($id) {
                $query->select(fields_dotted('guests'))
                    ->withPivot('main', 'active');
            },
            'guests.vehicles' => function ($query) use ($id) {
                $query->select(fields_dotted('vehicles'))
                    ->wherePivot('voucher_id', $id);
            },
            'guests.rooms' => function ($query) use ($id) {
                $query->select(fields_dotted('rooms'))
                    ->wherePivot('voucher_id', $id);
            },
            'guests.parent' => function ($query) {
                $query->select('id', 'name', 'last_name');
            },
            'guests.identificationType' => function ($query) {
                $query->select('id', 'type');
            },
            'company' => function ($query) {
                $query->select(fields_get('companies'));
            },
            'rooms' => function ($query) {
                $query->select(fields_dotted('rooms'))
                    ->withPivot('quantity', 'discount', 'subvalue', 'taxes', 'value', 'start', 'end', 'price', 'enabled');
            },
            'products' => function ($query) {
                $query->select(fields_dotted('products'))
                    ->withPivot('id', 'quantity', 'value', 'created_at');
            },
            'services' => function ($query) {
                $query->select(fields_dotted('services'))
                    ->withPivot('id', 'quantity', 'value', 'created_at');
            },
            'additionals' => function ($query) {
                $query->select(['id', 'description', 'billable','value', 'voucher_id', 'created_at']);
            },
            'payments' => function ($query)
            {
                $query->select(fields_get('payments'));
            },
            'props' => function ($query) {
                $query->select(fields_dotted('props'))
                    ->withPivot('quantity', 'value', 'created_at');
            },
        ];
    }
}
