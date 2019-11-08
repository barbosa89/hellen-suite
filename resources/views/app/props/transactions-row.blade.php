<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center dont-break-out">
            <p>
                {{ trans('transactions.' . $row->type) }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 align-self-center">
            <p>
                {{ $row->commentary }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ round($row->amount, 0) }}
            </p>
        </div>
        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->done_by }}
            </p>
        </div>
        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->created_at->format('Y-m-d H:m') }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            @if ($loop->first and $transactions->onFirstPage())
                <a href="{{ route('props.transactions.destroy', ['id' => Hashids::encode($prop->id), 'transaction' => Hashids::encode($row->id)]) }}" class="btn btn-link">
                    <i class="fa fa-trash"></i>
                </a>
            @else
                <a class="btn btn-link">
                    <i class="fa fa-ban"></i>
                </a>
            @endif
        </div>
    </div>
</div>