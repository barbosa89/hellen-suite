@if(isset($option['type']))
    @switch($option['type'])
        @case('divider')
            <div class="dropdown-divider"></div>
            @break

        @case('confirm')
            <li class="nav-item">
                @include('partials.modal-btn', [
                    'url' => $option['url'],
                    'option' => $option['option'],
                    'method' => $option['method']
                ])
            </li>
            @break

        @case('modal')
            <li class="nav-item">
                @include('partials.modal-form-btn', [
                    'option' => $option['option'],
                    'id' => $option['id']
                ])
            </li>
            @break

        @case('post')
            <li class="nav-item">
                <a class="nav-link" href="{{ $option['url'] }}" onclick="event.preventDefault(); document.getElementById('post-form').submit();">
                    {{ $option['option'] }}
                </a>

                <form id="post-form" action="{{ $option['url'] }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
            @break

        @case('hideable')
            @if($option['show'])
                <li class="nav-item">
                    <a class="nav-link" href="{{ $option['url'] }}"
                    {{ isset($option['id']) ? 'id=' . $option['id'] : '' }}
                    {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>
                        {{ $option['option'] }}
                    </a>
                </li>
            @endif
            @break

        @case('dropdown')
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ $option['option'] }}
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    @foreach($option['url'] as $suboption)
                        @include('partials.li-dropdown', ['option' => $suboption])
                    @endforeach
                </div>
            </li>
            @break

        @default
            <li class="nav-item">
                <a class="nav-link {{ isset($option['active']) ? 'active btn btn-secondary text-white' : '' }}" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
            </li>
    @endswitch
@else
    <li class="nav-item">
        <a class="nav-link {{ isset($option['active']) ? 'active btn btn-secondary text-white' : '' }}" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
    </li>
@endif