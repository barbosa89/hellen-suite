@php
    $name = explode(" ", config('app.name'));
@endphp

@foreach ($name as $item)
    @if ($loop->first)
        <span class="fw-bold">{{ $item }}</span>
    @else
        <span class="fw-light">{{ $item }}</span>
    @endif
@endforeach

