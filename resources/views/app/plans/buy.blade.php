@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('plans') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('plans.title'),
            'url' => '#',
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('landing.plans')</h2>

                <form action="{{ route('invoices.store') }}" method="POST">
                    @csrf()

                    <input type="hidden" name="plan_id" value="{{ id_encode($plan->id) }}" required>

                    <div class="form-group{{ $errors->has('type_id') ? ' has-error' : '' }}">
                        <label for="type_id">@lang('common.idType'):</label>
                        <select class="form-control selectpicker" title="{{ trans('users.chooseType') }}" name="type_id" id="type_id" required>
                            @foreach($types as $type)
                                @if($loop->first)
                                    <option selected value="{{ id_encode($type->id) }}">{{ trans('common.' . $type->type) }}</option>
                                @else
                                    <option value="{{ id_encode($type->id) }}">{{ trans('common.' . $type->type) }}</option>
                                @endif
                            @endforeach
                        </select>

                        @if ($errors->has('type_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('type_id') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('customer_dni') ? ' has-error' : '' }}">
                        <label for="customer_dni">@lang('common.idNumber'):</label>
                        <input type="text" class="form-control" name="customer_dni" id="customer_dni" value="{{ old('customer_dni') }}" required placeholder="{{ trans('common.required') }}" pattern="[0-9]+" minlength="5" maxlength="20">

                        @if ($errors->has('customer_dni'))
                            <span class="help-block">
                                <strong>{{ $errors->first('customer_dni') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('customer_name') ? ' has-error' : '' }}">
                        <label for="customer_name">@lang('common.name')(s):</label>
                        <input type="text" class="form-control" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" required placeholder="{{ trans('common.required') }}" minlength="3" maxlength="120">

                        @if ($errors->has('customer_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('customer_name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('currency_id') ? ' has-error' : '' }}">
                        <label for="currency_id">@lang('currencies.currency'):</label>
                        <select class="form-control selectpicker" title="{{ trans('common.chooseOption') }}" name="currency_id" id="currency_id" required>
                            @foreach($currencies as $currency)
                                @if($loop->first)
                                    <option selected value="{{ id_encode($currency->id) }}">{{ trans('currencies.' . Str::lower($currency->code)) }} - {{ $currency->code }}</option>
                                @else
                                    <option value="{{ id_encode($currency->id) }}">{{ trans('currencies.' . Str::lower($currency->code)) }} - {{ $currency->code }}</option>
                                @endif
                            @endforeach
                        </select>

                        @if ($errors->has('currency_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('currency_id') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('plan_type') ? ' has-error' : '' }}">
                        <label for="plan_type">Plan:</label>
                        <input type="text" class="form-control" name="plan_type" id="plan_type" value="{{ trans('plans.type.' . $plan->getType()) }}" placeholder="{{ trans('common.required') }}" readonly>
                    </div>

                    <div class="form-group{{ $errors->has('months') ? ' has-error' : '' }}">
                        <label for="months">@lang('common.months'):</label>
                        <input type="text" class="form-control" name="months" id="months" value="{{ $plan->months }}" placeholder="{{ trans('common.required') }}" readonly>
                    </div>

                    <div class="form-group{{ $errors->has('total') ? ' has-error' : '' }}">
                        <label for="total">@lang('common.total'):</label>
                        <input type="text" class="form-control" name="total" id="total" value="$ {{ number_format($plan->price, 2, ',', '.') }}" placeholder="{{ trans('common.required') }}" readonly>
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('landing.buy')</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">@lang('common.back')</a>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $('select#currency_id').change(function (e) {
            var rates = {
                '{{ id_encode($currencies->first()->id) }}': '$ {{ number_format($plan->price, 2, ',', '.') }}',
                '{{ id_encode($currencies->last()->id) }}': '$ {{ number_format($plan->getDollarPrice(), 2, ',', '.') }}',
            };

            $('input#total').attr('value', rates[this.value])
        })
    </script>
@endsection
