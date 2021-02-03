<?php

namespace App\Repositories;

use App\Models\Voucher;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Contracts\VoucherRepository as Repository;

class VoucherRepository implements Repository
{
    /**
     * @param integer $hotel
     * @param integer $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function paginate(int $hotel, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Voucher::owner()
            ->where('hotel_id', $hotel)
            ->filter($filters)
            ->latest()
            ->with(['hotel', 'guests', 'company', 'payments'])
            ->paginate($perPage);
    }

    /**
     * @param integer $hotel
     * @param array $filters
     * @return Collection
     */
    public function all(int $hotel, array $filters = []): Collection
    {
        return Voucher::owner()
            ->where('hotel_id', $hotel)
            ->filter($filters)
            ->latest()
            ->with(['hotel', 'guests', 'company', 'payments'])
            ->get();
    }

    /**
     * @param int $id
     * @throws Exception
     * @return \App\Models\Voucher
     */
    public function find(int $id): Voucher
    {
        return Voucher::owner()
            ->where('id', $id)
            ->with([
                'hotel' => function ($query)
                {
                    $query->select(fields_get('hotels'));
                }
            ])
            ->firstOrFail(fields_get('vouchers'));
    }

    /**
     * @param integer $hotel
     * @param array $data
     * @throws Exception
     * @return \App\Models\Voucher
     */
    public function create(int $hotel, array $data): Voucher
    {
        $voucher = new Voucher();
        $voucher->fill($data);

        $voucher->hotel()->associate($hotel);
        $voucher->user()->associate(id_parent());
        $voucher->saveOrFail();

        return $voucher;
    }

    /**
     * @param  integer $id
     * @param  array $data
     * @throws Exception
     * @return \App\Models\Voucher
     */
    public function update(int $id, array $data): Voucher
    {
        $voucher = $this->find($id);
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
        $voucher = Voucher::owner()
            ->id($id)
            ->open()
            ->first(fields_get('vouchers'));

        if (empty($voucher)) {
            return false;
        }

        return $voucher->delete();
    }

    /**
     * @param string $query
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $query): LengthAwarePaginator
    {
        return Voucher::owner()
            ->whereLike([
                'number',
                'guests.name',
                'guests.last_name',
                'guests.dni',
                'company.business_name',
                'hotel.business_name'
            ], $query)
            ->with([
                'hotel' => function ($query)
                {
                    $query->select(['id', 'business_name']);
                }
            ])
            ->paginate(config('settings.paginate'), fields_get('vouchers'));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function list(): Collection
    {
        return Voucher::owner()
            ->lodging()
            ->with(['hotel', 'guests', 'company', 'payments'])
            ->get(fields_dotted('vouchers'));
    }

    /**
     * @param int $id
     * @return \App\Models\Voucher
     */
    public function first(int $id): Voucher
    {
        return Voucher::owner()
            ->id($id)
            ->open()
            ->with(['guests', 'guests.identificationType', 'rooms', 'hotel'])
            ->first(fields_dotted('vouchers'));
    }
}
