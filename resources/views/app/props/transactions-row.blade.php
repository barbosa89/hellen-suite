<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center dont-break-out">
            <p>
                {{ trans('transactions.' . $row->type) }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-5 col-lg-5 align-self-center">
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
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => 'Option',
                        'url' => '#'
                    ],
                    [
                        'type' => 'divider'
                    ],
                    [
                        'option' => 'Option',
                        'url' => '#'
                    ]
                ]
            ])
        </div>
    </div>
</div>