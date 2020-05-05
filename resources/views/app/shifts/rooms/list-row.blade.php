<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('rooms.show', ['id' => id_encode($row->id)]) }}">
                    {{ $row->number }}
                </a>
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                @switch($row->status)
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
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                @if ($row->vouchers->isNotEmpty())
                    <a href="{{ route('vouchers.show', ['id' => id_encode($row->vouchers->first()->id)]) }}">
                        {{ $row->vouchers->first()->number }}
                    </a>
                @else
                    -
                @endif
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                $ {{ number_format($row->vouchers->sum('value'), 0, ',', '.') }}
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            @php
                $cash = 0;
                $row->vouchers->each(function ($voucher)  use (&$cash)
                {
                    $value = $voucher->payments->reduce(function ($carry, $payment) {
                        return $carry + $payment->value;
                    });

                    $cash += $value;
                })
            @endphp

            <p>
                $ {{ number_format($cash, 0, ',', '.') }}
            </p>
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 align-self-center">
            <a href="{{ route('rooms.show', ['id' => id_encode($row->id)]) }}">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    </div>
</div>