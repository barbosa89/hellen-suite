<?php

namespace App\Repositories;

use App\Contracts\CompanyRepository as Repository;
use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CompanyRepository implements Repository
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Company::query()
            ->whereOwner()
            ->latest()
            ->filter($filters)
            ->paginate($perPage);
    }

    public function all(array $filters = []): Collection
    {
        return Company::query()
            ->whereOwner()
            ->filter($filters)
            ->get();
    }

    public function find(int $id): Company
    {
        return Company::whereOwner()->where('id', $id);
    }

    public function create(array $data): Company
    {
        $company = new Company;
        $company->fill($data);
        $company->user()->associate(id_parent());
        $company->save();

        return $company;
    }

    public function update(int $id, array $data): Company
    {
        $company = $this->find($id);
        $company->fill($data);
        $company->save();

        return $company;
    }

    public function destroy(int $id): bool
    {
        $company = $this->find($id);

        return $company->delete();
    }

    public function search(string $query): LengthAwarePaginator
    {
        return Company::whereOwner()
            ->whereLike(['business_name', 'tin'], $query)
            ->paginate(15, fields_get('companies'));

    }
}
