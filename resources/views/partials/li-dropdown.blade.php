@if(isset($option['type']))
    @switch($option['type'])
        @case('divider')
            <div class="dropdown-divider"></div>
            @break

        @case('confirm')
            @if (isset($option['permission']))
                @can($option['permission'])
                    <a class="dropdown-item" href="#" data-url="{{ $option['url'] }}" data-method="{{ $option['method'] }}" id="modal-confirm" onclick="confirmAction(this, event)">
                        {{ $option['option'] }}
                    </a>
                @endcan
            @else
                <a class="dropdown-item" href="#" data-url="{{ $option['url'] }}" data-method="{{ $option['method'] }}" id="modal-confirm" onclick="confirmAction(this, event)">
                    {{ $option['option'] }}
                </a>
            @endif
            @break

        @case('modal')
            @if (isset($option['permission']))
                @can($option['permission'])
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#{{ $option['id'] }}">
                        {{ $option['option'] }}
                    </a>
                @endcan
            @else
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#{{ $option['id'] }}">
                    {{ $option['option'] }}
                </a>
            @endif
            @break

        @case('post')
            @if (isset($option['permission']))
                @can($option['permission'])
                    <a class="dropdown-item" href="{{ $option['url'] }}" onclick="event.preventDefault(); document.getElementById('post-form').submit();">
                        {{ $option['option'] }}
                    </a>

                    <form id="post-form" action="{{ $option['url'] }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                @endcan
            @else
                <a class="dropdown-item" href="{{ $option['url'] }}" onclick="event.preventDefault(); document.getElementById('post-form').submit();">
                    {{ $option['option'] }}
                </a>

                <form id="post-form" action="{{ $option['url'] }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            @endif
            @break

        @case('hideable')
            @if (isset($option['permission']))
                @can($option['permission'])
                    @if($option['show'])
                        <a class="dropdown-item" href="{{ $option['url'] }}"
                        {{ isset($option['id']) ? 'id=' . $option['id'] : '' }}
                        {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>
                            {{ $option['option'] }}
                        </a>
                    @endif
                @endcan
            @else
                @if($option['show'])
                    <a class="dropdown-item" href="{{ $option['url'] }}"
                    {{ isset($option['id']) ? 'id=' . $option['id'] : '' }}
                    {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>
                        {{ $option['option'] }}
                    </a>
                @endif
            @endif
            @break

        @default
            @if (isset($option['permission']))
                @can($option['permission'])
                    <a class="dropdown-item" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
                @endcan
            @else
                <a class="dropdown-item" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
            @endif
    @endswitch
@else
    @if (isset($option['permission']))
        @can($option['permission'])
            <a class="dropdown-item" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
        @endcan
    @else
        <a class="dropdown-item" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
    @endif
@endif