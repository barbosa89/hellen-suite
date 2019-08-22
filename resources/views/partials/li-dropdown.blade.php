@if(isset($option['type']))
    @switch($option['type'])
        @case('divider')
            <div class="dropdown-divider"></div>
            @break

        @case('confirm')
            <a class="dropdown-item" href="#" data-url="{{ $option['url'] }}" data-method="{{ $option['method'] }}" id="modal-confirm" onclick="confirmAction(this, event)">
                {{ $option['option'] }}
            </a>
            @break

        @case('modal')
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#{{ $option['id'] }}">
                {{ $option['option'] }}
            </a>
            @break

        @case('post')
            <a class="dropdown-item" href="{{ $option['url'] }}" onclick="event.preventDefault(); document.getElementById('post-form').submit();">
                {{ $option['option'] }}
            </a>

            <form id="post-form" action="{{ $option['url'] }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
            @break

        @case('hideable')
            @if($option['show'])
                <a class="dropdown-item" href="{{ $option['url'] }}"
                {{ isset($option['id']) ? 'id=' . $option['id'] : '' }}
                {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>
                    {{ $option['option'] }}
                </a>
            @endif
            @break

        @default
            <a class="dropdown-item" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
    @endswitch
@else
    <a class="dropdown-item" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
@endif