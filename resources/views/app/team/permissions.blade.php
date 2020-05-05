@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('member', $member) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('users.team'),
            'url' => route('team.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <h2 class="text-center">@lang('users.permissions')</h2>

        <div class="row my-4">
            <div class="col-6">
                <span>@lang('team.member'):</span>
                <div>
                    <h4>{{ $member->name }}</h4>
                </div>
            </div>

            <div class="col-6">
                <span>Rol:</span>
                <div>
                    <h4>{{ trans('users.' . $member->roles->first()->name) }}</h4>
                </div>
            </div>
        </div>

        <form action="{{ route('team.permissions.store', ['id' => id_encode($member->id)]) }}" method="POST">
            @csrf()

            @foreach ($permissions->chunk(4) as $chunked)
                <div class="row my-4">
                    @foreach ($chunked as $group => $value)
                        <div class="col-3">
                            <h4>{{ trans('modules.' . $group) }}</h4>
                            @foreach($value as $permission)
                                <div class="pretty p-icon p-smooth d-block my-2">
                                <input type="checkbox" name="permissions[]" value="{{ id_encode($permission->id) }}" {{ $member->permissions->contains($permission) ? 'checked' : '' }}/>
                                    <div class="state p-primary">
                                        <i class="icon fa fa-check"></i>
                                        <label>{{ trans('permissions.' . explode('.', $permission->name)[1]) }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach

            <button type="submit" class="btn btn-primary">
                @lang('common.assign')
            </button>
            <a href="{{ route('team.index') }}" class="btn btn-secondary">
                @lang('common.back')
            </a>
        </form>
    </div>

@endsection