<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
            <p>
                <a href="{{ route('vouchers.show', ['id' => id_encode($row->id)]) }}">
                    {{ $row->number }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
            <p>
                <a href="{{ route('hotels.show', ['id' => id_encode($row->hotel->id)]) }}">
                    {{ $row->hotel->business_name }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
            <p>
                @if (empty($row->company))
                    @if ($row->guests->isNotEmpty())
                        <a href="{{ route('guests.show', ['id' => id_encode($row->guests->where('pivot.main', true)->first()->id)]) }}">
                            {{ $row->guests->where('pivot.main', true)->first()->full_name }}
                        </a>
                    @endif
                @else
                    <a href="{{ route('companies.show', ['id' => id_encode($row->company->id)]) }}">
                        {{ $row->company->business_name }}
                    </a>
                @endif
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
            <p>
                {{ number_format($row->value, 0, ',', '.') }}
            </p>
        </div>
        <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1 align-self-center">
            @if ($row->losses)
                <p>
                    (@lang('vouchers.losses'))
                </p>
            @else
                @php
                    if ($row->value == 0) {
                        $percentage = 0;
                    } else {
                        $percentage = $row->payments->sum('value') / $row->value;
                    }
                @endphp

                <p>
                    {{ number_format( $percentage * 100, 2, ',', '.') }}%
                </p>
            @endif
        </div>
        <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1 align-self-center">
            <p>
                {{ $row->reservation ? 'Reserva' : 'Ingreso' }}
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1 align-self-center">
            <p>
                {{ $row->created_at->format('y-m-d') }}
            </p>
        </div>
        <div class="col-xs-12 col-sm-1 col-md-1 col-lg- align-self-center">
            <p>
                <i class="text-primary fa fa-{{ $row->open ? 'lock-open' : 'lock' }} fa-2x"></i>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('common.show'),
                        'url' => route('vouchers.show', ['id' => id_encode($row->id)]),
                        'permission' => 'vouchers.show'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('rooms.addRoom'),
                        'url' => route('vouchers.rooms', ['id' => id_encode($row->id)]),
                        'show' => $row->open,
                        'permission' => 'vouchers.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => $row->company ? trans('vouchers.linkNewCompany') : trans('vouchers.linkCompany'),
                        'url' => route('vouchers.companies.search', [
                            'id' => id_encode($row->id)
                        ]),
                        'show' => $row->open,
                        'permission' => 'vouchers.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('vouchers.registerGuests'),
                        'url' => route('vouchers.guests.search', ['id' => id_encode($row->id)]),
                        'show' => $row->open,
                        'permission' => 'vouchers.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('vouchers.loadProducts'),
                        'url' => route('vouchers.products', ['id' => id_encode($row->id)]),
                        'show' => !$row->reservation and $row->open,
                        'permission' => 'vouchers.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('vouchers.loadServices'),
                        'url' => route('vouchers.services', ['id' => id_encode($row->id)]),
                        'show' => !$row->reservation and $row->open,
                        'permission' => 'vouchers.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('vouchers.load.dining.services'),
                        'url' => route('vouchers.services', [
                            'id' => id_encode($row->id),
                            'type' => 'dining'
                        ]),
                        'show' => !$row->reservation and $row->open,
                        'permission' => 'vouchers.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('common.register') . ' ' . trans('vehicles.vehicle'),
                        'url' => route('vouchers.vehicles.search', ['id' => id_encode($row->id)]),
                        'show' => !$row->reservation and $row->open,
                        'permission' => 'vouchers.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('vouchers.load.external.services'),
                        'url' => route('vouchers.external.add', ['id' => id_encode($row->id)]),
                        'show' => !$row->reservation and $row->open,
                        'permission' => 'vouchers.edit'
                    ],
                    [
                        'type' => $row->open ? 'confirm' : 'hideable',
                        'option' => trans('vouchers.close'),
                        'url' => route('vouchers.close', [
                            'id' => id_encode($row->id)
                        ]),
                        'show' => $row->open,
                        'method' => 'POST',
                        'permission' => $row->open ? 'vouchers.close' : 'vouchers.open',
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete.item'),
                        'url' => route('vouchers.destroy', [
                            'id' => id_encode($row->id)
                        ]),
                        'method' => 'DELETE',
                        'permission' => 'vouchers.destroy'
                    ],
                ]
            ])
        </div>
    </div>
</div>
