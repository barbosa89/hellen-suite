@switch($status)
    @case(0)
        @lang('rooms.occupied')
        @break

    @case(1)
        @lang('rooms.available')
        @break

    @case(2)
        @lang('rooms.cleaning')
        @break

    @case(3)
        @lang('rooms.disabled')
        @break

    @case(4)
        @lang('rooms.maintenance')
        @break

@endswitch

