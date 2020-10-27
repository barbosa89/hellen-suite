<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <p><a href="{{ route('users.show', ['user' => id_encode($row->id)]) }}">{{ $row->name }}</a></p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
            <p><a href="{{ route('users.show', ['user' => id_encode($row->id)]) }}">{{ $row->email }}</a></p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <p>
                <i class="fas fa-{{ $row->status ? 'check' : 'times-circle' }}"></i>
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2">
            <p>
                <i class="fas fa-{{ empty($row->email_verified_at) ? 'times-circle' : 'check' }}"></i>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('common.show'),
                        'url' => route('users.show', ['user' => id_encode($row->id)]),
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete.item'),
                        'url' => route('users.destroy', [
                            'user' => id_encode($row->id)
                        ]),
                        'method' => 'DELETE'
                    ],
                ]
            ])
        </div>
    </div>
</div>
