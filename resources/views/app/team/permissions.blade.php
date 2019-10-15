@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('member', $member) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => 'Mi equipo',
            'url' => route('team.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">Asignación de permisos</h2>

                <div class="row mb-4">
                    <div class="col-12">Miembro del equipo:</div>
                    <div class="col-12 mt-2">
                        <h4>{{ $member->name }}</h4>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">Rol:</div>
                    <div class="col-12 mt-2">
                        <h4>{{ trans('users.' . $member->roles->first()->name) }}</h4>
                    </div>
                </div>

                <form action="{{ route('team.permissions.store', ['id' => Hashids::encode($member->id)]) }}" method="POST">
                    @csrf()

                    @foreach ($permissions->chunk(4) as $chunked)
                        <div class="row my-4">
                            @foreach ($chunked as $group => $value)
                                <div class="col-3">
                                    <h4>{{ trans('modules.' . $group) }}</h4>
                                    @foreach($value as $permission)
                                        <div class="pretty p-icon p-smooth d-block my-2">
                                        <input type="checkbox" name="permissions[]" value="{{ Hashids::encode($permission->id) }}" {{ $member->permissions->contains($permission) ? 'checked' : '' }}/>
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

                    <button type="submit" class="btn btn-primary">Asignar</button>
                    <a href="{{ route('team.index') }}" class="btn btn-secondary">Volver</a>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="spacer-md"></div>
            </div>
        </div>
    </div>

@endsection