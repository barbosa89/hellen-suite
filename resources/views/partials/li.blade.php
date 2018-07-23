@if(isset($option['type']))
    @switch($option['type'])
        @case('divider')
            <li class="divider"></li>
            @break

        @case('confirm')
            <li>
                @include('partials.modal-btn', [
                    'url' => $option['url'],
                    'option' => $option['option'],
                    'method' => $option['method']
                ])
            </li>
            @break

        @case('modal')
            <li>
                @include('partials.modal-form-btn', [
                    'option' => $option['option'],
                    'id' => $option['id']
                ])
            </li>
            @break

        @case('post')
            <li>
                <a href="{{ $option['url'] }}" onclick="event.preventDefault(); document.getElementById('post-form').submit();">
                    {{ $option['option'] }}
                </a>

                <form id="post-form" action="{{ $option['url'] }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
            @break

        @default
            @if(is_string($option['url']))
                @if(isset($option['active']) and $option['active'] == true)
                    <li class="active"><a href="{{ $option['url'] }}">{{ $option['option'] }}</a></li>
                @else
                    <li><a href="{{ $option['url'] }}">{{ $option['option'] }}</a></li>
                @endif
            @else
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        {{ $option['option'] }} <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        @foreach($option['url'] as $suboption)
                            @include('partials.li', ['option' => $suboption])
                        @endforeach
                    </ul>
                </li>
            @endif
    @endswitch
@else
    @if(is_string($option['url']))
        @if(isset($option['active']) and $option['active'] == true)
            <li class="active"><a href="{{ $option['url'] }}">{{ $option['option'] }}</a></li>
        @else
            <li><a href="{{ $option['url'] }}">{{ $option['option'] }}</a></li>
        @endif
    @else
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                {{ $option['option'] }} <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                @foreach($option['url'] as $suboption)
                    @include('partials.li', ['option' => $suboption])
                @endforeach
            </ul>
        </li>
    @endif
@endif