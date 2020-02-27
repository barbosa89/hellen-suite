@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('products') }}
@endsection

@section('content')

    <div id="page-wrapper">
        <product-transactions :hotels="{{ $hotels->toJson() }}" :companies="{{ $companies->toJson() }}"></product-transactions>
    </div>

@endsection
