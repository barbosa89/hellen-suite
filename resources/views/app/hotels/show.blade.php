@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('hotel', $hotel) }}
@endsection

@section('content')
    <!-- TODO: Reparar la generación de dropdown menu por modulo"-->
    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => 'Hoteles',
            'url' => route('hotels.index'),
            'options' => [
                [
                    'option' => trans('common.edit'),
                    'url' => route('hotels.edit', [
                        'room' => Hashids::encode($hotel->id)
                    ]),
                ],
                [
                    'option' => $hotel->status ? trans('common.disable') : trans('common.enable'),
                    'url' => route('hotels.toggle', [
                        'room' => Hashids::encode($hotel->id)
                    ]),
                ],
                [
                    'type' => 'confirm',
                    'option' => trans('common.delete'),
                    'url' => route('hotels.destroy', [
                        'room' => Hashids::encode($hotel->id)
                    ]),
                    'method' => 'DELETE'
                ],
            ]
        ])

        <div class="row mb-4">
            <div class="col-2"><b>Hotel</b></div>
            <div class="col-10">{{ $hotel->business_name }}</div>
            <div class="col-2"><b>NIT</b></div>
            <div class="col-10">{{ $hotel->tin }}</div>

            @if (!empty($hotel->main))
                <div class="col-2"><b>Hotel principal</b></div>
                <div class="col-10">{{ $hotel->main->business_name }}</div>
            @endif
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="spacer-xs"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>@lang('common.chart')</h3>

                <div class="well">
                    <h4>Gráfica aquí</h4>
                </div>
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection