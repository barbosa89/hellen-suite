@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('hotel settings', $hotel) }}
@endsection

@section('content')

    <div id="page-wrapper">
        <x-navigation title="{{ trans('settings.title') }}"
            url="{{ route('hotels.settings.index', ['hotel' => $hotel->hash]) }}">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="{{ route('hotels.index') }}" class="nav-link">
                        {{ trans('common.back') }}
                    </a>
                </li>
            </ul>
        </x-navigation>

        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">First</th>
                            <th scope="col">Last</th>
                            <th scope="col">Handle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">1</th>
                            <td>Mark</td>
                            <td>Otto</td>
                            <td>@mdo</td>
                        </tr>
                        <tr>
                            <th scope="row">2</th>
                            <td>Jacob</td>
                            <td>Thornton</td>
                            <td>@fat</td>
                        </tr>
                        <tr>
                            <th scope="row">3</th>
                            <td>Larry</td>
                            <td>the Bird</td>
                            <td>@twitter</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
