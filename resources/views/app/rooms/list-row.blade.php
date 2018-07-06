<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <p><a href="{{ route('rooms.show', ['room' => $row->id]) }}">{{ $row->number }}</a></p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 visible-md visible-lg">
            <p><a href="{{ route('rooms.show', ['room' => $row->id]) }}">{{ number_format($row->value, 2, ',', '.') }}</a></p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 visible-md visible-lg">
            <p>
                <a href="{{ route('rooms.show', ['room' => $row->id]) }}">
                    @include('partials.room-status', ['status' => $row->status])
                </a>
            </p>            
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('common.seeMore'),
                        'url' => route('rooms.show', ['room' => $row->id]),
                    ],
                    [
                        'option' => trans('assets.add'),
                        'url' => '#',
                    ],
                    [
                        'option' => trans('products.add'),
                        'url' => '#',
                    ],
                    [
                        'type' => 'divider'
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete'),
                        'url' => route('rooms.destroy', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE'
                    ],
                ]
            ])
        </div>
    </div>
</div>