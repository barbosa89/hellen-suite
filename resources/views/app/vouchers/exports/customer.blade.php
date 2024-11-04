@if (!empty($customer))
    <h3> {{ $customer['name'] }}</h3>
    <span class="d-block text-body-secondary fw-light">{{ $customer['tin'] }}</span>
    <span class="d-block text-body-secondary fw-light">{{ $customer['address'] ?? trans('common.noData') }}</span>
    <span class="d-block text-body-secondary fw-light">{{ $customer['phone'] ?? trans('common.noData') }}</span>
    <span class="d-block text-body-secondary fw-light">{{ $customer['email'] ?? trans('common.noData') }}</span>
@endif
