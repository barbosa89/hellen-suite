<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
            <a href="{{ route('hotels.show', ['id' => Hashids::encode($row->id)]) }}">
                <img class="img-fluid" src="{{ empty($row->image) ? asset('/images/hotel.png') : asset(Storage::url($row->image)) }}" alt="{{ $row->business_name }}">
            </a>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3   col-lg-3  align-self-center">
            <p>
                <a href="{{ route('hotels.show', ['id' => Hashids::encode($row->id)]) }}">
                    {{ $row->business_name }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 align-self-center">
            <p>
                <a href="{{ route('hotels.show', ['id' => Hashids::encode($row->id)]) }}">
                    {{ $row->tin }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('hotels.show', ['id' => Hashids::encode($row->id)]) }}">
                    {{ $row->created_at->format('Y-m-d') }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('hotels.show', ['id' => Hashids::encode($row->id)]) }}">
                    <i class="fa fa-{{ $row->status ? 'check-circle' : 'times-circle' }}"></i>
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
            @canany(['hotels.show', 'hotels.edit', 'hotels.destroy'])
                @include('partials.dropdown-btn', [
                    'options' => [
                        [
                            'option' => trans('common.show'),
                            'url' => route('hotels.show', [
                                'id' => Hashids::encode($row->id)
                            ]),
                            'permission' => 'hotels.show'
                        ],
                        [
                            'option' => trans('common.edit'),
                            'url' => route('hotels.edit', [
                                'id' => Hashids::encode($row->id)
                            ]),
                            'permission' => 'hotels.edit'
                        ],
                        [
                            'option' => $row->status ? trans('common.disable') : trans('common.enable'),
                            'url' => route('hotels.toggle', [
                                'id' => Hashids::encode($row->id)
                            ]),
                            'permission' => 'hotels.edit'
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete.item'),
                            'url' => route('hotels.destroy', [
                                'id' => Hashids::encode($row->id)
                            ]),
                            'method' => 'DELETE',
                            'permission' => 'hotels.destroy'
                        ],
                    ]
                ])
            @endcanany
        </div>
    </div>
</div>
