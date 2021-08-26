@switch($status)
    @case(0)
        @lang('guests.status.out')
        @break

    @case(1)
        @lang('guests.status.hosted')
        @break

    @default
        @lang('guests.status.out')
        @break

@endswitch

