<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 align-self-center">
            <p>
                <a href="{{ route('products.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->description }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('products.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->brand ?? trans('common.noData') }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('products.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->reference ?? trans('common.noData') }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('products.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ number_format($row->price, 2, ',', '.') }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
            <p>
                <a href="{{ route('products.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->quantity }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('products.increase'),
                        'url' => route('products.increase.form', [
                            'id' => Hashids::encode($row->id)
                        ]),
                    ],
                    [
                        'option' => trans('products.losses'),
                        'url' => '#'
                    ],
                    [
                        'type' => 'divider'
                    ],
                    [
                        'option' => trans('common.edit'),
                        'url' => route('products.edit', [
                            'room' => Hashids::encode($row->id)
                        ]),
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete'),
                        'url' => route('products.destroy', [
                            'room' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE'
                    ],
                ]
            ])
        </div>
    </div>
</div>