<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
            <p>
                <a href="{{ route('invoices.show', ['id' => Hashids::encode($row->id)]) }}">
                    {{ $row->number }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
            <p>
                <a href="{{ route('hotels.show', ['id' => Hashids::encode($row->hotel->id)]) }}">
                    {{ $row->hotel->business_name }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 align-self-center dont-break-out">
            <p>
                @if (empty($row->company))
                    @if ($row->guests->isNotEmpty())
                        <a href="{{ route('guests.show', ['id' => Hashids::encode($row->guests->where('pivot.main', true)->first()->id)]) }}">
                            {{ $row->guests->where('pivot.main', true)->first()->full_name }}
                        </a>
                    @endif
                @else
                    <a href="{{ route('companies.show', ['id' => Hashids::encode($row->company->id)]) }}">
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
                    (@lang('invoices.losses'))
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
                        'option' => trans('common.seeMore'),
                        'url' => route('invoices.show', ['id' => Hashids::encode($row->id)]),
                        'permission' => 'invoices.show'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('rooms.addRoom'),
                        'url' => route('invoices.rooms', ['id' => Hashids::encode($row->id)]),
                        'show' => $row->open,
                        'permission' => 'invoices.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => $row->company ? trans('invoices.linkNewCompany') : trans('invoices.linkCompany'),
                        'url' => route('invoices.companies.search', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'show' => $row->open,
                        'permission' => 'invoices.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('invoices.registerGuests'),
                        'url' => route('invoices.guests.search', ['id' => Hashids::encode($row->id)]),
                        'show' => $row->open,
                        'permission' => 'invoices.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('invoices.loadProducts'),
                        'url' => route('invoices.products', ['id' => Hashids::encode($row->id)]),
                        'show' => !$row->reservation and $row->open,
                        'permission' => 'invoices.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('invoices.loadServices'),
                        'url' => route('invoices.services', ['id' => Hashids::encode($row->id)]),
                        'show' => !$row->reservation and $row->open,
                        'permission' => 'invoices.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('invoices.load.dining.services'),
                        'url' => route('invoices.services', [
                            'id' => Hashids::encode($row->id),
                            'type' => 'dining'
                        ]),
                        'show' => !$row->reservation and $row->open,
                        'permission' => 'invoices.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('common.register') . ' ' . trans('vehicles.vehicle'),
                        'url' => route('invoices.vehicles.search', ['id' => Hashids::encode($row->id)]),
                        'show' => !$row->reservation and $row->open,
                        'permission' => 'invoices.edit'
                    ],
                    [
                        'type' => 'hideable',
                        'option' => trans('invoices.load.external.services'),
                        'url' => route('invoices.external.add', ['id' => Hashids::encode($row->id)]),
                        'show' => !$row->reservation and $row->open,
                        'permission' => 'invoices.edit'
                    ],
                    [
                        'type' => $row->open ? 'confirm' : 'hideable',
                        'option' => trans('invoices.close'),
                        'url' => route('invoices.close', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'show' => $row->open,
                        'method' => 'POST',
                        'permission' => $row->open ? 'invoices.close' : 'invoices.open',
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete.item'),
                        'url' => route('invoices.destroy', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE',
                        'permission' => 'invoices.destroy'
                    ],
                ]
            ])
        </div>
    </div>
</div>
