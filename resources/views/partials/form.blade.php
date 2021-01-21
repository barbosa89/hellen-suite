
@if (isset($title))
    <{{ isset($title['size']) ? $title['size'] : 'h2' }} class="{{ isset($title['align']) ? $title['align'] : 'text-left' }}">
        {{ $title['title'] }}
    </{{ isset($title['size']) ? $title['size'] : 'h2' }}>
@endif
<form action="{{ $url }}"
    {{ isset($horizontal) ? 'class=form-horizontal' : '' }}
    method="POST"
    accept-charset="utf-8"
    {{ isset($files) ? 'enctype=multipart/form-data' : '' }}
    {{ isset($id) ?? 'id=' . $id }}>
    @if(isset($csrf) and $csrf == false)
    @else
        {{ csrf_field() }}
    @endif

    @if(isset($method))
        <input type="hidden" name="_method" value="{{ $method }}">
    @endif

    @if(is_array($fields))
        @foreach($fields as $field)
            @include($field)
        @endforeach
    @else
        @include($fields)
    @endif

    @isset($btn)
        <button type="submit" class="btn btn-primary">{{ $btn }}</button>
    @endisset

    @isset($link)
        <a href="{{ $link['href'] }}" class="btn btn-link">{{ $link['name'] }}</a>
    @endisset
</form>
