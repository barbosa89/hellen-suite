@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('plans') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('plans.title'),
            'url' => '#',
            'options' => []
        ])

        <div class="row">
            @foreach ($plans as $plan)
                <div class="col-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
                    @include('app.plans.types.cards.' . $plan->getType())
                </div>
            @endforeach
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
