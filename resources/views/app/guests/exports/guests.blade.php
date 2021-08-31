<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ config('app.name') }}</title>
</head>
<body>
    <table>
        <thead>
        <tr>
            <th>@lang('common.idType')</th>
            <th>@lang('common.idNumber')</th>
            <th>@lang('common.name')</th>
            <th>@lang('common.lastname')</th>
            <th>@lang('guests.profession')</th>
            <th>@lang('common.gender')</th>
            <th>@lang('common.birthdate')</th>
            <th>@lang('guests.country')</th>
            <th>@lang('guests.registerDate')</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($guests as $guest)
                <tr>
                    <td>{{ trans('common.' . $guest->identificationType->type) }}</td>
                    <td>{{ $guest->dni }}</td>
                    <td>{{ $guest->name }}</td>
                    <td>{{ $guest->last_name }}</td>
                    <td>{{ $guest->profession ??  trans('common.noData') }}</td>
                    <td>{{ $guest->gender ? trans('common.' . $guest->gender) : trans('common.noData') }}</td>
                    <td>{{ $guest->birthdate ??  trans('common.noData') }}</td>
                    <td>{{ $guest->country ? $guest->country->name : trans('common.noData') }}</td>
                    <td>{{ $guest->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
