<div class="dropdown">
    <button class="btn btn-link dropdown-toggle w-btn-dropdown" type="button" data-toggle="dropdown" onclick="changeIcon(this, event, '{{ $icons['first'] }}', '{{ $icons['second'] }}')">
        <span class="fa {{ $icons['first'] }} fa-2x"></span>
    </button>
    <ul class="dropdown-menu">
        @foreach($options as $option)
            @if(isset($option['type']))
                @switch($option['type'])
                    @case('divider')
                        <li class="divider"></li>
                        @break
                
                    @case('post')
                        <li>
                            @include('partials.modal-btn', [
                                'url' => $option['url'],
                                'option' => $option['option']
                            ])
                        </li>
                        @break
                
                    @default
                    <li><a href="{{ $option['url'] }}">{{ $option['option'] }}</a></li>
                @endswitch
            @else
                <li><a href="{{ $option['url'] }}">{{ $option['option'] }}</a></li>
            @endif
        @endforeach
    </ul>
</div>