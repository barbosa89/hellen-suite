@php
    $name = explode(" ", config('app.name'));
@endphp

@if (count($name) > 0)
    <span class="font-weight-bold">{{ $name[0] }}</span><span class="font-weight-light">{{ $name[1] }}</span>
@else
    {{ config('app.name') }}
@endif
