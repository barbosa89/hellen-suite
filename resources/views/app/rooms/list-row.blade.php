<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <p>
                <a href="{{ route('rooms.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->number }}

                    @if ($row->is_suite)
                        <i class="fa fa-star"></i>
                    @endif
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 visible-md visible-lg">
            <p><a href="{{ route('rooms.show', ['room' => Hashids::encode($row->id)]) }}">{{ number_format($row->price, 2, ',', '.') }}</a></p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 visible-md visible-lg">
            <p>
                <a href="{{ route('rooms.show', ['room' => Hashids::encode($row->id)]) }}">
                    @include('partials.room-status', ['status' => $row->status])
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('common.seeMore'),
                        'url' => route('rooms.show', ['room' => Hashids::encode($row->id)]),
                    ],
                    [
                        'option' => trans('rooms.reserve'),
                        'url' => '#',
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
                        'option' => trans('common.edit'),
                        'url' => route('rooms.edit', [
                            'room' => Hashids::encode($row->id)
                        ]),
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete'),
                        'url' => route('rooms.destroy', [
                            'room' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE'
                    ],
                ]
            ])
        </div>
    </div>
</div>