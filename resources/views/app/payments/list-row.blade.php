<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->date }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 align-self-center">
            <p>
                {{ $row->commentary }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                @lang('payments.' . $row->payment_method)
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ number_format($row->value, 2, '.', ',') ?? trans('common.noData') }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
            <p>
                @if (empty($row->invoice))
                    @lang('common.noData')
                @else
                    <a href="{{ asset(Storage::url($row->invoice)) }}" target="_blank">
                        @lang('invoices.invoice')
                    </a>
                @endif
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            @if (!$invoice->payment_status)
                <a href="{{ route('payments.edit', ['invoice' => Hashids::encode($invoice->id), 'id' => Hashids::encode($row->id)]) }}" class="btn btn-link">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="#" data-url="{{ route('payments.destroy', ['invoice' => Hashids::encode($invoice->id), 'id' => Hashids::encode($row->id)]) }}" data-method="DELETE" id="modal-confirm" onclick="confirmAction(this, event)">
                    <i class="fas fa-times-circle"></i>
                </a>
            @endif
        </div>
    </div>
</div>