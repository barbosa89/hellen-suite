<h3 class="mb-4">
    @lang('invoices.invoice') No. <a href="{{ route('invoices.show', ['id' => Hashids::encode($invoice->id)] ) }}">
        {{ $invoice->number }}
    </a>

    @if ($invoice->reservation)
        <small>(Reservación)</small>
    @endif
</h3>