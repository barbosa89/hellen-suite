{{--
    - 0: Occupied
    - 1: Free
    - 2: Maintenance
    - 3: Disabled
--}}

@switch($status)
    @case(0)
        @lang('rooms.occupied')
        @break

    @case(1)
        @lang('rooms.available')
        @break

    @case(2)
        @lang('rooms.maintenance')
        @break

    @case(3)
        @lang('rooms.disabled')
        @break

    @case(4)
        @lang('rooms.cleaning')
        @break

@endswitch

