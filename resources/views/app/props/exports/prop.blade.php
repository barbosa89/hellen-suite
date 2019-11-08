<!DOCTYPE html>
<html lang="en">
<head>
    <title>Document</title>
</head>
<body>
    <table>
        <thead>
        <tr>
            <th>Tipo</th>
            <th>Comentario</th>
        </tr>
        </thead>
        <tbody>
            @foreach($prop->transactions as $transaction)
                <tr>
                    <td>{{ $transaction->type }}</td>
                    <td>{{ $transaction->commentary }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>