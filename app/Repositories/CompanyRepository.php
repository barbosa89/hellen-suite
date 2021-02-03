<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Contracts\CompanyRepository as Repository;

class CompanyRepository implements Repository
{
    /**
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Company::query()
            ->owner()
            ->latest()
            ->filter($filters)
            ->paginate($perPage);
    }

    /**
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function all(array $filters = []): Collection
    {
        return Company::query()
            ->owner()
            ->filter($filters)
            ->get();
    }

    /**
     * @param integer $id
     * @return \App\Models\Company
     */
    public function find(int $id): Company
    {
        return Company::owner()->where('id', $id);
    }

    public function create(array $data): Company
    {
        $company = new Company();
        $company->fill($data);
        $company->user()->associate(id_parent());
        $company->save();

        return $company;
    }

    /**
     * @param integer $id
     * @param array $data
     * @return \App\Models\Company
     */
    public function update(int $id, array $data): Company
    {
        $company = $this->find($id);
        $company->fill($data);
        $company->save();

        return $company;
    }

    /**
     * @param integer $id
     * @return boolean
     */
    public function destroy(int $id): bool
    {
        $company = $this->find($id);

        return $company->delete();
    }

    /**
     * @param string $query
     * @return LengthAwarePaginator
     */
    public function search(string $query): LengthAwarePaginator
    {
        return Company::owner()
            ->whereLike(['business_name', 'tin'], $query)
            ->paginate(15 , fields_get('companies'));

    }
}
