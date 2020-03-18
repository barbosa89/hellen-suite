@include('partials.dropdown-btn', [
    'options' => [
        [
            'option' => trans('common.show'),
            'url' => route('rooms.show', ['id' => Hashids::encode($row->id)]),
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
            'option' => trans('common.edit'),
            'url' => route('rooms.edit', [
                'id' => Hashids::encode($row->id)
            ]),
        ],
        [
            'type' => 'confirm',
            'option' => trans('common.delete.item'),
            'url' => route('rooms.destroy', [
                'id' => Hashids::encode($row->id)
            ]),
            'method' => 'DELETE'
        ],
    ]
])
