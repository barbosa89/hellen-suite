<!DOCTYPE html>
<html lang="en">
<head>
    <title>Document</title>
</head>
<body>
    <table>
        <thead>
        <tr>
            <th>Hotel</th>
            <th>@lang('companies.tin')</th>
            <th>@lang('common.description')</th>
            <th>Transacciones de entrada</th>
            <th>Transacciones de salida</th>
            <th>Cantidad total en entradas</th>
            <th>Cantidad total en salidas</th>
            <th>Promedio mensual de entradas</th>
            <th>Promedio mensual de salidas</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($hotels as $hotel)
                @foreach ($hotel->props as $prop)
                    <tr>
                        <td>{{ $hotel->business_name }}</td>
                        <td>{{ $hotel->tin }}</td>
                        <td>{{ $prop->description }}</td>
                        <td>{{ $prop->transactions->where('type', 'input')->count() }}</td>
                        <td>{{ $prop->transactions->where('type', 'output')->count() }}</td>
                        <td>{{ $prop->transactions->where('type', 'input')->sum('amount') }}</td>
                        <td>{{ $prop->transactions->where('type', 'output')->sum('amount') }}</td>
                        <td>{{ $prop->transactions->where('type', 'input')->sum('amount') / 12 }}</td>
                        <td>{{ $prop->transactions->where('type', 'output')->sum('amount') / 12 }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>