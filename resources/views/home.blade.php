@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('home') }}
@endsection

@section('content')
    @if (session()->has('flash_notification'))
        <div class="row">
            <div class="col">
                <div class="mt-4">
                    @include('flash::message')
                </div>
            </div>
        </div>
    @endif

    @unlessrole('root')
        <home-index :user-name='"{{ auth()->user()->name }}"'></home-index>
    @endunlessrole
@endsection
