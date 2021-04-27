<?php

namespace App\Http\Controllers\Api;

use App\Rules\MinDate;
use App\Models\Voucher;
use App\Contracts\VoucherRepository;
use App\Helpers\GuestChart;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    public VoucherRepository $voucher;

    public function __construct(VoucherRepository $voucher)
    {
        $this->voucher = $voucher;
    }

    /**
     * Display a listing of the paginate resource.
     *
     * @param  string $hotel
     * @return \Illuminate\Http\Response
     */
    public function index(string $hotel)
    {
        $filters = request()->validate([
            'from_date' => [
                'bail',
                'nullable',
                'date',
                'before_or_equal:today',
                new MinDate(),
            ],
            'status.*' => [
                'bail',
                'nullable',
                'string',
                Rule::in(Voucher::STATUS),
            ],
            'type.*' => [
                'bail',
                'nullable',
                'string',
                Rule::in(Voucher::TYPES),
            ],
            'search' => [
                'bail',
                'nullable',
                'alpha_num',
                'max:30',
                'min:3'
            ],
        ]);

        $perPage = request()->input('per_page', config('settings.paginate'));

        $vouchers = $this->voucher->paginate(id_decode($hotel), $perPage, $filters);

        return response()->json([
            'vouchers' => $vouchers,
        ]);
    }

    /**
     * @param  string  $hotelId
     * @param  string  $period
     * @return \Illuminate\Http\Response
     */
    public function getGuestDataset(string $hotelId, string $pediod)
    {
        Validator::make(['period' => $pediod], [
            'period' => 'required|date',
        ])->validate();

        $startDate = Carbon::parse($pediod)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $vouchers = $this->voucher->queryGuestChecks(id_decode($hotelId), $startDate, $endDate);

        $guestChart = new GuestChart($vouchers, $startDate, $endDate);
        $chartData = $guestChart->countChecks()->get();

        return response()->json($chartData);
    }
}
