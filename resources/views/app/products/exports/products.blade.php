<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ config('app.name') }}</title>
</head>
<body>
    <table>
        <thead>
        <tr>
            <th>Hotel</th>
            <th>@lang('common.description')</th>
            <th>@lang('common.date') @lang('common.of') @lang('vouchers.voucher')</th>
            <th>@lang('common.number') @lang('common.of') @lang('vouchers.voucher')</th>
            <th>@lang('common.type') @lang('common.of') @lang('vouchers.voucher')</th>
            <th>@lang('common.value') @lang('common.of') @lang('vouchers.voucher')</th>
            <th>@lang('common.quantity') @lang('common.of') @lang('vouchers.voucher')</th>
            <th>@lang('transactions.made.by')</th>
            <th>@lang('companies.company') @lang('common.of') @lang('vouchers.voucher')</th>
            <th>@lang('common.comments')</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($hotels as $hotel)
                @foreach ($hotel->products as $product)
                    @foreach ($product->vouchers as $voucher)
                        <tr>
                            <td>{{ $hotel->business_name }}</td>
                            <td>{{ $product->description }}</td>
                            <td>{{ $voucher->created_at->format('Y-m-d') }}</td>
                            <td>{{ $voucher->number }}</td>
                            <td>{{ trans('transactions.' . $voucher->type) }}</td>
                            <td>{{ number_format($voucher->pivot->value, 2, ',', '.') }}</td>
                            <td>{{ $voucher->pivot->quantity }}</td>
                            <td>{{ $voucher->made_by }}</td>
                            <td>{{ empty($voucher->company) ? trans('common.doesnt.apply') : $voucher->company->business_name }}</td>
                            <td>{{ $voucher->comments ?? trans('common.noData') }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>