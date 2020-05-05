@include('partials.dropdown-btn', [
    'options' => [
        [
            'option' => trans('common.show'),
            'url' => route('rooms.display', ['id' => id_encode($row->id)]),
        ],
        [
            'option' => trans('rooms.reserve'),
            'url' => '#',
        ]
    ]
])
