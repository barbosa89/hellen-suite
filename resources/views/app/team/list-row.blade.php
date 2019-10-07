<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
            <a href="{{ route('hotels.show', ['room' => Hashids::encode($row->id)]) }}">
                {{ $row->name }}
            </a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('hotels.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->email }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('hotels.show', ['room' => Hashids::encode($row->id)]) }}">
                    {{ $row->hotels()->count() > 0 ? $row->hotels()->first()->business_name : 'No asignado' }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('hotels.show', ['room' => Hashids::encode($row->id)]) }}">
                    <i class="fa fa-{{ $row->status ? 'check-circle' : 'times-circle' }}"></i>
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('hotels.show', ['room' => Hashids::encode($row->id)]) }}">
                    <i class="fa fa-{{ $row->verified ? 'check-circle' : 'times-circle' }}"></i>
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