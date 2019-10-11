<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('team.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->name }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('team.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->email }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('team.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->roles()->count() > 0 ? trans('users.' . $row->roles()->first()->name) : 'No asignado' }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('team.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->headquarters()->count() > 0 ? $row->headquarters()->first()->business_name : 'No asignado' }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('team.show', ['room' => Hashids::encode($row->id)]) }}">
                    <i class="fa fa-{{ empty($row->email_verified_at) ? 'times-circle' : 'check-circle' }}"></i>
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => 'Ver',
                        'url' => route('team.show', [
                            'id' => Hashids::encode($row->id)
                        ]),
                    ],
                    [
                        'option' => 'Asignaciones',
                        'url' => route('team.assign', [
                            'room' => Hashids::encode($row->id)
                        ]),
                    ],
                    [
                        'option' => 'Permisos',
                        'url' => route('team.permissions', [
                            'room' => Hashids::encode($row->id)
                        ]),
                    ],
                    [
                        'type' => 'divider'
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