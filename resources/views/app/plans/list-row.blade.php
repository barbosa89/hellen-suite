<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
            <p>
                {{ trans('plans.descriptions.' . Str::lower($row->type)) }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3   col-lg-3  align-self-center">
            <p>
                {{ $row->months }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 align-self-center">
            <p>
                {{ number_format($row->price, 2, ',', '.') }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            @if ($row->status)
                <p class="text-success">
                    <i class="fa fa-check-circle"></i>
                </p>
            @else
                <p class="text-danger">
                    <i class="fa fa-times-circle"></i>
                </p>
            @endif
        </div>
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('common.edit'),
                        'url' => route('plans.edit', [
                            'id' => id_encode($row->id)
                        ])
                    ]
                ]
            ])
        </div>
    </div>
</div>
