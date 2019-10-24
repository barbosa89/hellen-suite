<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
            <p>
                <a href="{{ route('invoices.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->number }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <p>
                <a href="{{ route('invoices.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ number_format($row->value, 2, ',', '.') }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <p>
                <a href="{{ route('invoices.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ empty($row->hotel) ? 'No asignado' : $row->hotel->business_name }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <p>
                <a href="{{ route('invoices.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->created_at->format('Y-m-d') }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-1 col-lg-1">
            <p>
                <a href="{{ route('invoices.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->reservation ? 'Reserva' : 'Ingreso' }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1">
            <p>
                <a href="{{ route('invoices.show', ['room' => Hashids::encode($row->id)]) }}">
                    <i class="fa fa-{{ $row->open ? 'check' : 'times-circle' }}"></i>
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1">
            <p>
                <a href="{{ route('invoices.show', ['room' => Hashids::encode($row->id)]) }}">
                    <i class="fa fa-{{ $row->status ? 'check' : 'times-circle' }}"></i>
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('common.seeMore'),
                        'url' => route('invoices.show', ['id' => Hashids::encode($row->id)]),
                    ],
                    [
                        'option' => trans('rooms.addRoom'),
                        'url' => route('invoices.rooms', ['id' => Hashids::encode($row->id)]),
                    ],
                    [
                        'type' => 'hideable',
                        'option' => 'Agregar empresa',
                        'url' => route('invoices.companies.search', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'show' => empty($row->company) ? true : false
                    ],
                    [
                        'option' => trans('invoices.registerGuests'),
                        'url' => route('invoices.guests.search', ['id' => Hashids::encode($row->id)]),
                    ],
                    [
                        'option' => trans('invoices.loadProducts'),
                        'url' => route('invoices.products', ['id' => Hashids::encode($row->id)]),
                    ],
                    [
                        'option' => trans('invoices.loadServices'),
                        'url' => route('invoices.services', ['id' => Hashids::encode($row->id)]),
                    ],
                    [
                        'option' => 'Agregar servicios de terceros',
                        'url' => '#',
                    ],
                    [
                        'type' => 'divider'
                    ],
                    [
                        'option' => trans('common.close'),
                        'url' => "#"
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete'),
                        'url' => route('invoices.destroy', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE'
                    ],
                ]
            ])
        </div>
    </div>
</div>