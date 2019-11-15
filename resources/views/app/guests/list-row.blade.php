<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 align-self-center">
            <p><a href="{{ route('guests.show', ['room' => Hashids::encode($row->id)]) }}">{{ $row->full_name }}</a></p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 align-self-center">
            <p><a href="{{ route('guests.show', ['room' => Hashids::encode($row->id)]) }}">{{ $row->dni }}</a></p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 align-self-center">
            <p>
                <a href="{{ route('guests.show', ['room' => Hashids::encode($row->id)]) }}">
                    @include('partials.guest-status', ['status' => $row->status])
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('common.seeMore'),
                        'url' => route('guests.show', ['room' => Hashids::encode($row->id)]),
                    ],
                    [
                        'type' => 'divider'
                    ],
                    [
                        'option' => trans('common.edit'),
                        'url' => route('guests.edit', [
                            'room' => Hashids::encode($row->id)
                        ]),
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete'),
                        'url' => route('guests.destroy', [
                            'room' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE'
                    ],
                ]
            ])
        </div>
    </div>
</div>