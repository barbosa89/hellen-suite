<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <a href="{{ route('hotels.show', ['room' => Hashids::encode($row->id)]) }}">
                {{ $row->name }}
            </a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 align-self-center">
            <p>
                <a href="{{ route('hotels.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->email }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 align-self-center">
            <p>
                <a href="{{ route('hotels.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->headquarters()->count() > 0 ? $row->headquarters()->first()->business_name : 'No asignado' }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('hotels.show', ['room' => Hashids::encode($row->id)]) }}">
                    <i class="fa fa-{{ empty($row->email_verified_at) ? 'times-circle' : 'check-circle' }}"></i>
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => 'Ver',
                        'url' => route('team.show', [
                            'id' => Hashids::encode($row->id)
                        ]),
                    ],
                    [
                        'type' => 'divider'
                    ],
                    [
                        'option' => trans('common.edit'),
                        'url' => route('team.edit', [
                            'room' => Hashids::encode($row->id)
                        ]),
                    ],
                    [
                        'option' => $row->status ? 'Deshabilitar' : 'Habilitar',
                        'url' => route('team.toggle', [
                            'room' => Hashids::encode($row->id)
                        ]),
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete'),
                        'url' => route('team.destroy', [
                            'room' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE'
                    ],
                ]
            ])
        </div>
    </div>
</div>