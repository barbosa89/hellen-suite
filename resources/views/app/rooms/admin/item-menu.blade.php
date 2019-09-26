@include('partials.dropdown-btn', [
    'options' => [
        [
            'option' => trans('common.seeMore'),
            'url' => route('rooms.show', ['room' => Hashids::encode($row->id)]),
        ],
        [
            'option' => trans('assets.add'),
            'url' => '#',
        ],
        [
            'option' => trans('products.add'),
            'url' => '#',
        ],
        [
            'type' => 'divider'
        ],
        [
            'option' => trans('common.edit'),
            'url' => route('rooms.edit', [
                'room' => Hashids::encode($row->id)
            ]),
        ],
        [
            'type' => 'confirm',
            'option' => trans('common.delete'),
            'url' => route('rooms.destroy', [
                'room' => Hashids::encode($row->id)
            ]),
            'method' => 'DELETE'
        ],
    ]
])