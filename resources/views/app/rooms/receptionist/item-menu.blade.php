@include('partials.dropdown-btn', [
    'options' => [
        [
            'option' => trans('common.seeMore'),
            'url' => route('rooms.display', ['id' => Hashids::encode($row->id)]),
        ],
        [
            'option' => trans('rooms.reserve'),
            'url' => '#',
        ]
    ]
])
