<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ config('app.name') }}</title>
</head>
<body>
    <table>
        <thead>
        <tr>
            <th colspan="2">
                @lang('common.description')
            </th>
            <th colspan="2">
                @lang('common.quantity')
            </th>
            <th colspan="2">
                @lang('common.price')
            </th>
            <th colspan="2">
                Hotel
            </th>
        </tr>
        <tr>
            <th colspan="2">
                {{ $product->description }}
            </th>
            <th colspan="2">
                {{ $product->quantity }}
            </th>
            <th colspan="2">
                {{ number_format($product->price, 2, ',', '.') }}
            </th>
            <th colspan="2">
                {{ $product->hotel->business_name }}
            </th>
        </tr>
        <tr>
            <th>&nbsp;</th>
        </tr>
        <tr>
            <th>@lang('common.date')</th>
            <th>@lang('common.number')</th>
            <th>@lang('common.type')</th>
            <th>@lang('common.value')</th>
            <th>@lang('common.quantity')</th>
            <th>@lang('transactions.made.by')</th>
            <th>@lang('companies.company')</th>
            <th>@lang('common.comments')</th>
        </tr>
        </thead>
        <tbody>
            @foreach($product->vouchers as $voucher)
                <tr>
                    <td>{{ $voucher->created_at->format('Y-m-d') }}</td>
                    <td>{{ $voucher->number }}</td>
                    <td>{{ trans('transactions.' . $voucher->type) }}</td>
                    <td>{{ number_format($voucher->value, 2, ',', '.') }}</td>
                    <td>{{ $voucher->pivot->quantity }}</td>
                    <td>{{ $voucher->made_by }}</td>
                    <td>{{ empty($voucher->company) ? trans('common.doesnt.apply') : $voucher->company->business_name }}</td>
                    <td>{{ $voucher->comments ?? trans('common.noData') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>