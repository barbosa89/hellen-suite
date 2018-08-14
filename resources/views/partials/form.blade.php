
<{{ isset($title['size']) ? $title['size'] : 'h2' }} class="{{ isset($title['align']) ? $title['align'] : 'text-left' }}">
    {{ $title['title'] }}
</{{ isset($title['size']) ? $title['size'] : 'h2' }}>
<form action="{{ $url }}" 
    method="{{ isset($method) ? $method : 'POST' }}" 
    accept-charset="utf-8" 
    {{ isset($files) and $files ?? 'enctype="multipart/form-data"' }}
    {{ isset($id) ?? 'id=' . $id }}>
    @if(isset($csrf) and $csrf == false)
    @else
        @csrf()
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
</form> 