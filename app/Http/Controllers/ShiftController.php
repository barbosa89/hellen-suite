<?php

namespace App\Http\Controllers;

use App\Welkome\Shift;
use App\Helpers\{Fields, Id};
use App\Welkome\Room;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shifts = Shift::where('team_member', auth()->user()->id)
            ->whereHas('user', function ($query)
            {
                $query->where('id', Id::parent());
            })->with([
                'hotel' => function ($query)
                {
                    $query->select(Fields::get('hotels'));
                }
            ])->get(Fields::get('shifts'));

        return view('app.shifts.index', compact('shifts'));
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $shift = Shift::where('team_member', auth()->user()->id)
            ->where('id', Id::get($id))
            ->with([
                'vouchers' => function($query)
                {
                    $query->select(Fields::get('vouchers'));
                },
                'vouchers.payments' => function($query)
                {
                    $query->select(Fields::get('payments'));
                },
                'hotel' => function($query)
                {
                    $query->select(Fields::get('hotels'));
                }
            ])->first(Fields::get('shifts'));

        $cash = $this->filterByPaymentMethod($shift, 'cash');
        $transfer = $this->filterByPaymentMethod($shift, 'transfer');
        $courtesy = $this->filterByPaymentMethod($shift, 'courtesy');

        $rooms = Room::where('user_id', Id::parent())
                    ->where('hotel_id', $shift->hotel->id)
                    ->with([
                        'vouchers' => function ($query)
                        {
                            $query->select(['vouchers.id', 'vouchers.value', 'vouchers.number']);
                        },
                        'vouchers.payments' => function ($query)
                        {
                            $query->select(['id', 'value', 'voucher_id']);
                        }
                    ])->get(['id', 'number', 'status']);

        return view('app.shifts.show', compact('shift', 'cash', 'transfer', 'courtesy', 'rooms'));
    }

    /**
     * Filter vouchers by payment method
     *
     * @param \App\Welkome\Shift $shift
     * @param string $method
     * @return \Illuminate\Support\Collection
     */
    private function filterByPaymentMethod(Shift $shift, string $method): Collection
    {
        if (!in_array($method, ['cash', 'transfer', 'courtesy'])) {
            throw new InvalidArgumentException("Unknown payment method: " . $method, 1);
        }

        return $shift->vouchers->filter(function ($voucher) use ($method)
        {
            $results = $voucher->payments->where('payment_method', $method);

            return $results->count() > 0;
        });
    }
}
