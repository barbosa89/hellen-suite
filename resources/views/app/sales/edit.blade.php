@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('hotel', $hotel) }}
@endsection

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
                <h2 class="text-center">@lang('common.editionOf') Hotel</h2>

                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">Info!</h4>
                    Los hoteles y sedes, una vez creados, no puede ser modificados debido a restricciones con respecto al histórico de registros
                </div>

                <div class="row mb-4">
                    <div class="col-12">Hotel:</div>
                    <div class="col-12 mt-2">
                        <h4>{{ $hotel->business_name }}</h4>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">NIT:</div>
                    <div class="col-12 mt-2">
                        <h4>{{ $hotel->tin }}</h4>
                    </div>
                </div>

                @if (!empty($hotel->main))
                    <div class="row mb-4">
                        <div class="col-12">Hotel principal:</div>
                        <div class="col-12 mt-2">
                            <h4>{{ $hotel->main->business_name }}</h4>
                        </div>
                    </div>
                @endif

                <form action="{{ route('hotels.update', ['id' => Hashids::encode($hotel->id)]) }}" method="POST" enctype="multipart/form-data">
                    @csrf()
                    @method('PUT')

                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="address">@lang('common.address'):</label>
                        <input type="text" class="form-control" name="address" id="address" value="{{ $hotel->address }}" maxlength="100" required>

                        @if ($errors->has('address'))
                            <span class="help-block">
                                <strong>{{ $errors->first('address') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone">@lang('common.phone'):</label>
                        <input type="string" class="form-control" name="phone" id="phone" value="{{ $hotel->phone }}" maxlength="10" pattern="\d{7,10}" title="1230987, 0371230987" required>

                        @if ($errors->has('phone'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                        <label for="mobile">@lang('common.mobile'):</label>
                        <input type="string" class="form-control" name="mobile" id="mobile" value="{{ $hotel->mobile }}" maxlength="10" pattern="\d{10}" title="3151230987" required>

                        @if ($errors->has('mobile'))
                            <span class="help-block">
                                <strong>{{ $errors->first('mobile') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email">@lang('common.email'):</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ $hotel->email }}" maxlength="100" required>

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                        <label for="image">Logotipo: <small>Se recomienda imágenes cuadradas o de proporción 3/2, peso máximo 200 Kb. Sino agrega un logotipo, se mostrará uno por defecto.</small></label>
                        <input type="file" class="form-control" name="image" id="image" value="{{ old('image') }}" accept="image/png, image/jpeg">

                        @if ($errors->has('image'))
                            <span class="help-block">
                                <strong>{{ $errors->first('image') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('common.update')</button>
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
