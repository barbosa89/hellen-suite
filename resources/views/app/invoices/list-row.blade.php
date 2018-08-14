<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <p><a href="{{ route('invoices.show', ['room' => Hashids::encode($row->id)]) }}">{{ $row->number }}</a></p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 visible-md visible-lg">
            <p><a href="{{ route('invoices.show', ['room' => Hashids::encode($row->id)]) }}">{{ number_format($row->value, 2, ',', '.') }}</a></p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 visible-md visible-lg">
            <p>
                <a href="{{ route('invoices.show', ['room' => Hashids::encode($row->id)]) }}">
                    @include('partials.room-status', ['status' => $row->status])
                </a>
            </p>            
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
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