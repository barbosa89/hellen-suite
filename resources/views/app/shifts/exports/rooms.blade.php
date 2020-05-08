<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ trans('rooms.title') }}</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>@lang('common.number')</th>
                <th>@lang('common.status')</th>
                <th>@lang('vouchers.voucher')</th>
                <th>@lang('common.value')</th>
                <th>@lang('payments.title')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rooms as $room)
                <tr>
                    <td>
                        <a href="{{ route('rooms.show', ['id' => id_encode($room->id)]) }}">
                            {{ $room->number }}
                        </a>
                    </td>
                    <td>
                        @switch($room->status)
                            @case(0)
                                @lang('rooms.occupied')
                                @break
                            @case(1)
                                @lang('rooms.available')
                                @break
                            @case(2)
                                @lang('rooms.cleaning')
                                @break
                            @case(3)
                                @lang('rooms.disabled')
                                @break
                            @case(4)
                                @lang('rooms.maintenance')
                                @break

                        @endswitch
                    </td>
                    <td>
                        @if ($room->vouchers->isNotEmpty())
                            {{ $room->vouchers->first()->number }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $room->vouchers->sum('value') }}</td>
                    <td>
                        @php
                            $cash = 0;
                            $room->vouchers->each(function ($voucher)  use (&$cash)
                            {
                                $value = $voucher->payments->reduce(function ($carry, $payment) {
                                    return $carry + $payment->value;
                                });

                                $cash += $value;
                            })
                        @endphp

                        {{ $cash }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>