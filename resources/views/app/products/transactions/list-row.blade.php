<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
            <p>
                {{ $row->created_at->format('Y-m-d') }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->commentary }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                @lang('transactions.' . $row->type)
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->quantity }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ number_format($row->quantity * $row->value, 2, ',', '.') }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->done_by }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
            {{-- <a href="{{ route('assets.maintenance.edit', ['id' => Hashids::encode($asset->id), 'maintenance' => Hashids::encode($row->id)]) }}" class="btn btn-link">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" data-url="{{ route('assets.maintenance.destroy', ['id' => Hashids::encode($asset->id), 'maintenance' => Hashids::encode($row->id)]) }}" data-method="DELETE" id="modal-confirm" onclick="confirmAction(this, event)">
                <i class="fas fa-times-circle"></i>
            </a> --}}
        </div>
    </div>
</div>