<tr>
    <td>
        <a href="{{ route('invoices.show', ['invoice' => id_encode($row->id)]) }}">
            {{ $row->number }}
        </a>
    </td>
    <td>{{ $row->customer_name }}</td>
    <td>{{ Str::upper($row->identificationType->type) }} {{ $row->customer_dni }}</td>
    <td>{{ $row->currency->code }} ${{ number_format($row->total, 2, ',', '.') }}</td>
    <td>{{ trans('invoices.status.' . Str::lower($row->status)) }}</td>
    <td>{{ $row->created_at->format('Y-m-d') }}</td>
    <td>
        @include('partials.dropdown-btn', [
            'options' => [
                [
                    'option' => trans('common.show'),
                    'url' => route('invoices.show', [
                        'invoice' => id_encode($row->id)
                    ])
                ],
                [
                    'type' => $row->status != \App\Models\Invoice::PAID ? 'confirm' : 'hideable',
                    'option' => trans('common.delete.item'),
                    'url' => route('invoices.destroy', [
                        'invoice' => id_encode($row->id)
                    ]),
                    'method' => 'DELETE',
                    'show' => $row->status != \App\Models\Invoice::PAID
                ],
            ]
        ])
    </td>
</tr>

