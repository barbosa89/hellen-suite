<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 align-self-center">
            <p>
                @can('guests.show')
                    <a href="{{ route('guests.show', ['id' => Hashids::encode($row->id)]) }}">{{ $row->full_name }}</a>
                @endcan
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 align-self-center">
            <p>
                @can('guests.show')
                    <a href="{{ route('guests.show', ['id' => Hashids::encode($row->id)]) }}">{{ $row->dni }}</a>
                @endcan
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 align-self-center">
            <p>
                @can('guests.show')
                    <a href="{{ route('guests.show', ['id' => Hashids::encode($row->id)]) }}">
                        @include('partials.guest-status', ['status' => $row->status])
                    </a>
                @endcan
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('common.seeMore'),
                        'url' => route('guests.show', ['id' => Hashids::encode($row->id)]),
                        'permission' => 'guests.show'
                    ],
                    [
                        'option' => trans('common.edit'),
                        'url' => route('guests.edit', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'permission' => 'guests.edit'
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete.item'),
                        'url' => route('guests.destroy', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE',
                        'permission' => 'guests.destroy'
                    ],
                ]
            ])
        </div>
    </div>
</div>
