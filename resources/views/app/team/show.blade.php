@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('member', $member) }}
@endsection

@section('content')
    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('team.title'),
            'url' => route('team.index'),
            'options' => [
                [
                    'option' => trans('common.assign'),
                    'url' => route('team.assign', [
                        'id' => id_encode($member->id)
                    ]),
                    'permission' => 'team.edit'
                ],
                [
                    'option' => trans('users.permissions'),
                    'url' => route('team.permissions', [
                        'id' => id_encode($member->id)
                    ]),
                    'permission' => 'team.edit'
                ],
                [
                    'option' => trans('common.edit'),
                    'url' => route('team.edit', [
                        'id' => id_encode($member->id)
                    ]),
                ],
                [
                    'type' => 'confirm',
                    'option' => trans('common.delete.item'),
                    'url' => route('team.destroy', [
                        'id' => id_encode($member->id)
                    ]),
                    'method' => 'DELETE'
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.name'):</h3>
                <p>{{ $member->name }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.email'):</h3>
                <p>{{ $member->email }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('users.role'):</h3>
                <p>{{ $member->roles->isNotEmpty() ? trans('users.' . $member->roles->first()->name) : trans('common.not.assigned') }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>Hotel:</h3>
                <p>{{ $member->headquarters->isNotEmpty() ? $member->headquarters->first()->business_name : trans('common.not.assigned') }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $member->shifts,
                    'listHeading' => 'app.shifts.list-heading',
                    'listRow' => 'app.shifts.list-row',
                    'where' => null
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
