<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ config('app.name') }}</title>
</head>
<body>
    <table>
        <thead>
        <tr>
            <th>@lang('companies.tin')</th>
            <th>@lang('common.name')</th>
            <th>@lang('common.email')</th>
            <th>@lang('common.address')</th>
            <th>@lang('common.phone')</th>
            <th>@lang('common.mobile')</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
                <tr>
                    <td>{{ $company->tin }}</td>
                    <td>{{ $company->business_name }}</td>
                    <td>{{ $company->email ??  trans('common.noData') }}</td>
                    <td>{{ $company->address ??  trans('common.noData') }}</td>
                    <td>{{ $company->phone ??  trans('common.noData') }}</td>
                    <td>{{ $company->mobile ??  trans('common.noData') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>