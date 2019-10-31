@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('products') }}
@endsection

@section('content')

    <div id="page-wrapper">
        <product-list :hotels="{{ $hotels->toJson() }}"></product-list>

        @include('partials.modal-confirm')
    </div>

@endsection