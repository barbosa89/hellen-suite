@if (!empty($customer))
    <h3> {{ $customer['name'] }}</h3>
    <span class="d-block font-weight-light">{{ $customer['tin'] }}</span>
    <span class="d-block font-weight-light">{{ $customer['address'] ?? trans('common.noData') }}</span>
    <span class="d-block font-weight-light">{{ $customer['phone'] ?? trans('common.noData') }}</span>
    <span class="d-block font-weight-light">{{ $customer['email'] ?? trans('common.noData') }}</span>
@endif