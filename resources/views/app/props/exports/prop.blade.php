<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ config('app.name') }}</title>
</head>
<body>
    <table>
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Comentario</th>
            <th>Cantidad</th>
            <th>Hecho por</th>
        </tr>
        </thead>
        <tbody>
            @foreach($prop->transactions as $transaction)
                <tr>
                    <td>{{ $transaction->created_at }}</td>
                    <td>{{ trans('transactions.' . $transaction->type) }}</td>
                    <td>{{ $transaction->commentary }}</td>
                    <td>{{ round($transaction->amount, 0) }}</td>
                    <td>{{ $transaction->done_by }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>