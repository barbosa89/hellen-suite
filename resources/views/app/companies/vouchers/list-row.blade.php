<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 align-self-center">
            <p>
                {{ $row->created_at->format('Y-m-d') }}
            </p>
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 align-self-center">
            <p>
                <a href="{{ route('vouchers.show', ['id' => Hashids::encode($row->id)]) }}">
                    {{ $row->number }}
                </a>
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->hotel->business_name }}
            </p>
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 align-self-center">
            <p>
                @lang('transactions.' . $row->type)
            </p>
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 align-self-center">
            <p>
                {{ number_format($row->value, 2, ',', '.') }}
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->comments ? str_limit($row->comments, 100) : trans('common.noData') }}
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->made_by }}
            </p>
        </div>
        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 align-self-center">
            <a href="{{ route('vouchers.show', ['id' => Hashids::encode($row->id)]) }}" class="btn btn-link">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    </div>
</div>