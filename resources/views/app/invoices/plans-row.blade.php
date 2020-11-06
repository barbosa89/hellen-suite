<tr>
    <td>${{ number_format($row->price, 2, ',', '.') }}</td>
    <td>{{ $row->months }}</td>
    <td>{{ trans('plans.type.' . Str::lower($row->type)) }}</td>
</tr>

