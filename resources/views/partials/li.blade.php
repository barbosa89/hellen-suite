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

        @default
            @if(is_string($option['url']))
                <li class="nav-item">
                    <a class="nav-link" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
                </li>
            {{-- @else
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ $option['option'] }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        @foreach($option['url'] as $suboption)
                            @include('partials.li', ['option' => $suboption])
                        @endforeach
                    </div>
                </li> --}}
            @endif
    @endswitch
@else
    @if(is_string($option['url']))
        <li class="nav-item">
            <a class="nav-link" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
        </li>
    {{-- @else
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ $option['option'] }}
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                @foreach($option['url'] as $suboption)
                    @include('partials.li', ['option' => $suboption])
                @endforeach
            </div>
        </li> --}}
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Drop
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="#">Action</a>
              <a class="dropdown-item" href="#">Another action</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#">Something else here</a>
            </div>
          </li>
    @endif
@endif