@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('vouchers') }}
@endsection

@section('content')
    <vouchers-index></vouchers-index>

    @include('partials.modal-confirm')
@endsection
