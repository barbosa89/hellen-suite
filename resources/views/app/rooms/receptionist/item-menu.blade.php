@include('partials.dropdown-btn', [
    'options' => [
        [
            'option' => trans('common.show'),
            'url' => route('rooms.display', ['id' => Hashids::encode($row->id)]),
        ],
        [
            'option' => trans('rooms.reserve'),
            'url' => '#',
        ]
    ]
])
