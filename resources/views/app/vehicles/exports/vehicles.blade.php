<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ config('app.name') }}</title>
</head>
<body>
    <table>
        <thead>
        <tr>
            <th>@lang('vehicles.registration')</th>
            <th>@lang('common.brand')</th>
            <th>Color</th>
            <th>@lang('common.type')</th>
            <th>@lang('vehicles.drivers')</th>
            <th>@lang('common.date')</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($vehicles as $vehicle)
                <tr>
                    <td>{{ $vehicle->registration }}</td>
                    <td>{{ $vehicle->brand ?? trans('common.noData') }}</td>
                    <td>{{ $vehicle->color ?? trans('common.noData') }}</td>
                    <td>{{ trans('vehicles.' . $vehicle->type->type) }}</td>
                    <td>{{ $vehicle->guests->isNotEmpty() ? $vehicle->guests->implode('full_name', ',') : trans('common.noData') }}</td>
                    <td>{{ $vehicle->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>