<div class="row">
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <h3>@lang('companies.businessName'):</h3>
        <p>
            <a href="{{ route('companies.show', ['id' => id_encode($company->id)]) }}">
                {{ $company->business_name }}
            </a>
        </p>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
        <h2><small>@lang('companies.tin')</small>:</h2>
        <p>{{ $company->tin }}</p>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <h2>@lang('common.email'):</h2>
        {{ $company->email ?? trans('common.noData') }}
    </div>
</div>

<div class="row mb-4">
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <h3>@lang('common.address'):</h3>
        <p>{{ $company->address ?? trans('common.noData') }}</p>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <h3>@lang('common.phone'):</h3>
        <p>{{ $company->phone }} {{ $company->mobile ?? trans('common.noData') }}</p>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <h3>@lang('common.type'):</h3>
        @if ($company->is_supplier)
            <p>@lang('companies.is.supplier')</p>
        @else
            <p>@lang('companies.isnt.supplier')</p>
        @endif
    </div>
</div>