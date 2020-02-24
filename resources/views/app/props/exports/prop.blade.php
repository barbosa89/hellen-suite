<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ config('app.name') }}</title>
</head>
<body>
    <table>
        <thead>
        <tr>
            <th>@lang('common.date')</th>
            <th>@lang('common.number')</th>
            <th>@lang('common.type')</th>
            <th>@lang('common.value')</th>
            <th>@lang('common.quantity')</th>
            <th>@lang('transactions.made.by')</th>
            <th>@lang('common.supplier')</th>
            <th>@lang('companies.tin')</th>
            <th>@lang('common.comments')</th>
        </tr>
        </thead>
        <tbody>
            @foreach($prop->vouchers as $voucher)
                <tr>
                    <td>{{ $voucher->created_at->format('Y-m-d') }}</td>
                    <td>{{ $voucher->number }}</td>
                    <td>{{ trans('transactions.' . $voucher->type) }}</td>
                    <td>{{ number_format($voucher->value, 2, ',', '.') }}</td>
                    <td>{{ $voucher->pivot->quantity }}</td>
                    <td>{{ $voucher->made_by }}</td>
                    <td>{{ empty($voucher->company) ? trans('common.doesnt.apply') : $voucher->company->business_name }}</td>
                    <td>{{ empty($voucher->company) ? trans('common.doesnt.apply') : $voucher->company->tin }}</td>
                    <td>{{ $voucher->comments ?? trans('common.noData') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>