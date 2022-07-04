@php
    $name = explode(" ", config('app.name'));
@endphp

@foreach ($name as $item)
    @if ($loop->first)
        <span class="font-weight-bold">{{ $item }}</span>
    @else
        <span class="font-weight-light">{{ $item }}</span>
    @endif
@endforeach

