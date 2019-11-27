<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-5 col-lg-5 align-self-center">
            <p><a href="{{ route('companies.show', ['id' => Hashids::encode($row->id)]) }}">{{ $row->business_name }}</a></p>
        </div>
        <div class="col-xs-12 col-sm-5 col-md-5 col-lg-5 align-self-center">
            <p><a href="{{ route('companies.show', ['id' => Hashids::encode($row->id)]) }}">{{ $row->tin }}</a></p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('common.seeMore'),
                        'url' => route('companies.show', ['id' => Hashids::encode($row->id)]),
                    ],
                    [
                        'type' => 'divider'
                    ],
                    [
                        'option' => trans('common.edit'),
                        'url' => route('companies.edit', [
                            'id' => Hashids::encode($row->id)
                        ]),
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete'),
                        'url' => route('companies.destroy', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE'
                    ],
                ]
            ])
        </div>
    </div>
</div>