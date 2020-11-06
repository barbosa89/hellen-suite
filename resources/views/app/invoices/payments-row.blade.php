<tr>
    <td>${{ number_format($row->value, 2, ',', '.') }}</td>
    <td>{{ $row->payment_method }}</td>
    <td>{{ trans('payments.confirmation.status.' . Str::lower($row->status)) }}</td>
    <td>{{ $row->created_at->format('Y-m-d') }}</td>
</tr>

