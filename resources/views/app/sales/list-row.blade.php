<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->created_at->format('Y-m-d H') }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 align-self-center">
            <p>
                {{ $row->commentary }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->quantity }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ number_format($row->value, 2, '.', ',') }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
                @can('sales.edit')
                    <a href="{{ route('sales.edit', ['id' => Hashids::encode($product->id), 'sale' => Hashids::encode($row->id)]) }}" class="btn btn-link">
                        <i class="fas fa-edit"></i>
                    </a>
                @endcan

                @can('sales.destroy')
                    <a href="#" data-url="{{ route('sales.destroy', ['id' => Hashids::encode($product->id), 'sale' => Hashids::encode($row->id)]) }}" data-method="DELETE" id="modal-confirm" onclick="confirmAction(this, event)">
                        <i class="fas fa-times-circle"></i>
                    </a>
                @endcan
        </div>
    </div>
</div>
