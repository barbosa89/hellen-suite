@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('home') }}
@endsection

@section('content')
    <div class="row">
        <div class="col">
            <div class="mt-4">
                @include('flash::message')
            </div>
        </div>
    </div>

    @unlessrole('root')
        <home-index></home-index>
    @endunlessrole
@endsection
