@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('process') }}
@endsection

@section('content')

    <div id="page-wrapper">
        <process-list :hotels="{{ $hotels->toJson() }}"></process-list>

        @include('partials.modal-confirm')
    </div>

    @include('partials.modal-confirm')

@endsection