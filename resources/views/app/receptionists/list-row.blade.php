<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <p>{{ $row->name }}</p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 visible-md visible-lg">
            <p>{{ $row->email }}</p>
        </div>
        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 visible-md visible-lg">
            @if($row->status)
                <p>@lang('users.active')</p>
            @else
                <p>@lang('users.inactive')</p>
            @endif
        </div>
        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 visible-md visible-lg">
            @if($row->verified)
                <p>@lang('users.verified')</p>
            @else
                <p>@lang('users.notVerified')</p>
            @endif
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
            @include('partials.dropdown-btn', [
                'icons' => [
                    'first' => 'fa-caret-down', 
                    'second' => 'fa-caret-up', 
                ],
                'options' => [
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete'),
                        'url' => route('receptionists.destroy', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE'
                    ],
                ]
            ])
        </div>
    </div>
</div>