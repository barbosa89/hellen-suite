@include('partials.dropdown-btn', [
    'options' => [
        [
            'option' => trans('common.seeMore'),
            'url' => route('rooms.display', ['room' => Hashids::encode($row->id)]),
        ],
        [
            'option' => trans('rooms.reserve'),
            'url' => '#',
        ]
    ]
])