@php
    $form = Str::random(8);
@endphp

<a class="dropdown-item" href="{{ $option['url'] }}" onclick="event.preventDefault(); document.getElementById('post-form-{{ $form }}').submit();">
    {{ $option['option'] }}
</a>

<form id="post-form-{{ $form }}" action="{{ $option['url'] }}" method="POST" style="display: none;">
    {{ csrf_field() }}

    @if (isset($option['inputs']) and is_array($option['inputs']))
        @foreach ($option['inputs'] as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    @endif
</form>
