@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
            <div class="col-lg-12">
                <div class="page-header">
                    <h1>Panel de control</h1>
                </div>
                <div class="well">
                    <h2>¡Bienvenido, {{ auth()->user()->name }}!</h1>
                </div>
            </div>
        </div>
    </div>
@endsection
