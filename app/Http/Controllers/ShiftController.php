<?php

namespace App\Http\Controllers;

use App\Exports\ShiftReport;
use App\Models\Shift;
use App\Models\Note;
use App\Models\Room;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shifts = Shift::where('user_id', id_parent())
            ->when(!auth()->user()->hasRole(['manager', 'admin']), function ($query)
            {
                $query->where('team_member', auth()->user()->id);
            })->with([
                'hotel' => function ($query)
                {
                    $query->select(fields_get('hotels'));
                }
            ])->get(fields_get('shifts'));

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
        $shift = Shift::where('user_id', id_parent())
            ->when(!auth()->user()->hasRole(['manager', 'admin']), function ($query)
            {
                $query->where('team_member', auth()->user()->id);
            })->where('id', id_decode($id))
            ->with([
                'vouchers' => function($query)
                {
                    $query->select(fields_get('vouchers'));
                },
                'vouchers.payments' => function($query)
                {
                    $query->select(fields_get('payments'));
                },
                'hotel' => function($query)
                {
                    $query->select(fields_get('hotels'));
                },
                'notes' => function ($query)
                {
                    $query->select(Note::getColumnNames())
                        ->orderBy('created_at', 'DESC');
                },
                'notes.tags' => function ($query)
                {
                    $query->select(['id', 'slug']);
                }
            ])->first(fields_get('shifts'));

        $cash = $this->filterByPaymentMethod($shift, 'cash');
        $transfer = $this->filterByPaymentMethod($shift, 'transfer');
        $courtesy = $this->filterByPaymentMethod($shift, 'courtesy');

        $rooms = Room::where('user_id', id_parent())
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
     * @param \App\Models\Shift $shift
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

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function export(string $id)
    {
        $shift = Shift::where('user_id', id_parent())
            ->when(!auth()->user()->hasRole(['manager', 'admin']), function ($query)
            {
                $query->where('team_member', auth()->user()->id);
            })->where('id', id_decode($id))
            ->with([
                'vouchers' => function($query)
                {
                    $query->select(fields_get('vouchers'));
                },
                'vouchers.payments' => function($query)
                {
                    $query->select(fields_get('payments'));
                },
                'hotel' => function($query)
                {
                    $query->select(fields_get('hotels'));
                },
                'notes' => function ($query)
                {
                    $query->select(Note::getColumnNames())
                        ->orderBy('created_at', 'DESC');
                },
                'notes.tags' => function ($query)
                {
                    $query->select(['id', 'slug']);
                }
            ])->first(fields_get('shifts'));

        $rooms = Room::where('user_id', id_parent())
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

        return Excel::download(new ShiftReport($shift, $rooms), trans('shifts.shift') . '.xlsx');
    }

    /**
     * Close the shift.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function close(string $id)
    {
        $shift = Shift::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->where('open', true)
            ->where('closed_at', null)
            ->first(fields_get('shifts'));

        abort_if(empty($shift), 404);

        $shift->open = false;
        $shift->closed_at = now();

        if ($shift->save()) {
            flash(trans('common.successful'))->success();

            return redirect()->route('shifts.show', [
                'id' => id_encode($shift->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }
}
