<div class="tab-pane fade in {{ $active ? 'active' : '' }}" id="{{ $id }}">
    @if($users->where('status', $status)->count() > 0)
        <div class="crud-list">
            <div class="crud-list-heading">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                        <h5>@lang('common.name')</h5>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 visible-md visible-lg">
                        <h5>@lang('common.emailOnly')</h5>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 visible-md visible-lg">
                        <h5>@lang('common.status')</h5>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 visible-md visible-lg">
                        <h5>@lang('users.verification')</h5>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                        <h5>@lang('common.options')</h5>
                    </div>
                </div>
            </div>
            <div class="crud-list-items">
                @foreach($users->where('status', $status) as $user)
                    <div class="crud-list-row">
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                <p>{{ $user->name }}</p>
                            </div>
                            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 visible-md visible-lg">
                                <p>{{ $user->email }}</p>
                            </div>
                            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 visible-md visible-lg">
                                @if($user->status)
                                    <p>@lang('users.active')</p>
                                @else
                                    <p>@lang('users.inactive')</p>
                                @endif
                            </div>
                            <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 visible-md visible-lg">
                                @if($user->verified)
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
                                            'type' => 'post',
                                            'option' => trans('common.delete'),
                                            'url' => route('receptionists.destroy', [
                                                'id' => Hashids::encode($user->id)
                                            ]),
                                            'id' => 'delete-form-' . $loop->iteration,
                                            'confirm' => trans('common.confirm')
                                        ],
                                        [
                                            'type' => 'divider'
                                        ],
                                        [
                                            'option' => 'Test1',
                                            'url' => '#',
                                        ],
                                    ]
                                ])
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="well w-well">
            <h2>@lang('common.noRecords')</h2>
        </div>
    @endif
</div>