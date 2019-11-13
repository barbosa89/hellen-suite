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
            <th>@lang('companies.tin')</th>
            <th>@lang('common.number')</th>
            <th>@lang('common.description')</th>
            <th>@lang('common.brand')</th>
            <th>@lang('common.model')</th>
            <th>@lang('common.reference')</th>
            <th>@lang('assets.location')</th>
            <th>@lang('rooms.room')</th>
            <th>@lang('common.date')</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($hotels as $hotel)
                @foreach ($hotel->assets as $asset)
                    <tr>
                        <td>{{ $hotel->business_name }}</td>
                        <td>{{ $hotel->tin }}</td>
                        <td>{{ $asset->number }}</td>
                        <td>{{ $asset->description }}</td>
                        <td>{{ $asset->brand ??  trans('common.noData') }}</td>
                        <td>{{ $asset->model ??  trans('common.noData') }}</td>
                        <td>{{ $asset->reference ??  trans('common.noData') }}</td>
                        <td>{{ $asset->location ??  trans('common.noData') }}</td>
                        <td>{{ $asset->room ? $asset->room->number : trans('common.noData') }}</td>
                        <td>{{ $asset->created_at }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>