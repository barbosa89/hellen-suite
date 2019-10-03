@extends('layouts.panel')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => 'Hoteles',
            'url' => route('hotels.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('common.creationOf') Hotel</h2>
                <form action="{{ route('hotels.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf()

                    <div class="form-group{{ $errors->has('business_name') ? ' has-error' : '' }}">
                        <label for="business_name">Razón social: <small>{{ trans('common.required') }}</small></label>
                        <input type="text" class="form-control" name="business_name" id="business_name" value="{{ old('business_name') }}" required maxlength="191" placeholder="Nombre del hotel o sede">

                        @if ($errors->has('business_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('business_name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('tin') ? ' has-error' : '' }}">
                        <label for="tin">NIT: <small>{{ trans('common.required') }}</small></label>
                        <input type="text" class="form-control" name="tin" id="tin" value="{{ old('tin') }}" maxlength="30" placeholder="Formato: 123.456.789-0" required pattern="\d{3}\.\d{3}\.\d{3}\-\d{1}">

                        @if ($errors->has('tin'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tin') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="address">@lang('common.address'):</label>
                        <input type="text" class="form-control" name="address" id="address" value="{{ old('address') }}" maxlength="100">

                        @if ($errors->has('address'))
                            <span class="help-block">
                                <strong>{{ $errors->first('address') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone">@lang('common.phone'):</label>
                        <input type="string" class="form-control" name="phone" id="phone" value="{{ old('phone') }}" maxlength="10" pattern="\d{7,10}" title="1230987, 0371230987">

                        @if ($errors->has('phone'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                        <label for="mobile">@lang('common.mobile'):</label>
                        <input type="string" class="form-control" name="mobile" id="mobile" value="{{ old('mobile') }}" maxlength="10" pattern="\d{10}" title="3151230987">

                        @if ($errors->has('mobile'))
                            <span class="help-block">
                                <strong>{{ $errors->first('mobile') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email">@lang('common.email'):</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" maxlength="100">

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                        <label for="type">@lang('common.type'):</label>
                        <select class="form-control selectpicker" name="type" id="type" required>
                            @if ($hotels->isEmpty())
                                <option value="main" selected>Hotel independiente</option>
                            @else
                                <option value="main">Hotel independiente</option>
                                <option value="headquarter">Sede de hotel</option>
                            @endif
                        </select>

                        @if ($errors->has('type'))
                            <span class="help-block">
                                <strong>{{ $errors->first('type') }}</strong>
                            </span>
                        @endif
                    </div>

                    @if($hotels->isNotEmpty())
                        <div class="form-group{{ $errors->has('main_hotel') ? ' has-error' : '' }}" id="main-hotel" style="display:none;">
                            <label for="main_hotel">Sede principal:</label>
                            <select class="form-control selectpicker" name="main_hotel" id="main_hotel">
                                @foreach ($hotels as $hotel)
                                    <option value="{{ Hashids::encode($hotel->id) }}">{{ $hotel->business_name }}</option>
                                @endforeach
                            </select>

                            @if ($errors->has('main_hotel'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('main_hotel') }}</strong>
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                        <label for="image">Logotipo: <small>Se recomienda imágenes cuadradas o de proporción 3/2, peso máximo 200 Kb. Sino agrega un logotipo, se mostrará uno por defecto.</small></label>
                        <input type="file" class="form-control" name="image" id="image" value="{{ old('image') }}" accept="image/png, image/jpeg">

                        @if ($errors->has('image'))
                            <span class="help-block">
                                <strong>{{ $errors->first('image') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('common.create')</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Volver</a>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="spacer-md"></div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $("#type").on('change', function(e) {
            if (this.value == 'headquarter') {
                if ($('#main-hotel').is(':hidden')) {
                    $('#main-hotel').fadeIn();
                }
            } else {
                if ($('#main-hotel').is(':visible')) {
                    $('#main-hotel').fadeOut();
                }
            }
        });
    </script>
@endsection