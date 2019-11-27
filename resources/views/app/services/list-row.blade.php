<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 align-self-center">
            <p>
                <a href="{{ route('services.show', ['id' => Hashids::encode($row->id)]) }}">
                    {{ $row->description }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('services.show', ['id' => Hashids::encode($row->id)]) }}">
                    {{ number_format($row->price, 2, ',', '.') }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p class="text-primary">
                <i class="fas fa-{{ $row->status ? 'check' : 'times-circle' }}"></i>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            @include('partials.dropdown-btn', [
                'options' => [
                    [
                        'option' => trans('common.edit'),
                        'url' => route('services.edit', [
                            'id' => Hashids::encode($row->id)
                        ])
                    ],
                    [
                        'option' => $row->status ? 'Deshabilitar' : 'Habilitar',
                        'url' => route('services.toggle', ['id' => Hashids::encode($row->id)])
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete'),
                        'url' => route('services.destroy', [
                            'id' => Hashids::encode($row->id)
                        ]),
                        'method' => 'DELETE'
                    ],
                ]
            ])
        </div>
    </div>
</div>