<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
            <p><a href="{{ route('companies.show', ['room' => Hashids::encode($row->id)]) }}">{{ $row->business_name }}</a></p>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 visible-md visible-lg">
            <p><a href="{{ route('companies.show', ['room' => Hashids::encode($row->id)]) }}">{{ $row->tin }}</a></p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('common.seeMore'),
                        'url' => route('companies.show', ['room' => Hashids::encode($row->id)]),
                    ],
                    [
                        'type' => 'divider'
                    ],
                    [
                        'option' => trans('common.edit'),
                        'url' => route('companies.edit', [
                            'room' => Hashids::encode($row->id)
                        ]),
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete'),
                        'url' => route('companies.destroy', [
                            'room' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE'
                    ],
                ]
            ])
        </div>
    </div>
</div>