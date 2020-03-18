<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('team.show', ['id' => Hashids::encode($row->id)]) }}">
                    {{ $row->name }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('team.show', ['id' => Hashids::encode($row->id)]) }}">
                    {{ $row->email }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('team.show', ['id' => Hashids::encode($row->id)]) }}">
                    {{ $row->roles->isNotEmpty() ? trans('users.' . $row->roles->first()->name) : trans('common.not.assigned') }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('team.show', ['id' => Hashids::encode($row->id)]) }}">
                    {{ $row->headquarters->isNotEmpty() ? $row->headquarters->first()->business_name : trans('common.not.assigned') }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('team.show', ['id' => Hashids::encode($row->id)]) }}">
                    <i class="fa fa-{{ empty($row->email_verified_at) ? 'times-circle' : 'check-circle' }}"></i>
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('common.show'),
                        'url' => route('team.show', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'permission' => 'team.show'
                    ],
                    [
                        'option' => trans('common.assign'),
                        'url' => route('team.assign', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'permission' => 'team.edit'
                    ],
                    [
                        'option' => trans('users.permissions'),
                        'url' => route('team.permissions', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'permission' => 'team.edit'
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete.item'),
                        'url' => route('team.destroy', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE',
                        'permission' => 'team.destroy'
                    ],
                ]
            ])
        </div>
    </div>
</div>
