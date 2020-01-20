@if(isset($option['type']))
    @switch($option['type'])
        @case('divider')
            <div class="dropdown-divider"></div>
            @break

        @case('confirm')
            @if (isset($option['permission']))
                @can($option['permission'])
                    <li class="nav-item">
                        @include('partials.modal-btn', [
                            'url' => $option['url'],
                            'option' => $option['option'],
                            'method' => $option['method']
                        ])
                    </li>
                @endcan
            @else
                <li class="nav-item">
                    @include('partials.modal-btn', [
                        'url' => $option['url'],
                        'option' => $option['option'],
                        'method' => $option['method']
                    ])
                </li>
            @endif
            @break

        @case('modal')
            @if (isset($option['permission']))
                @can($option['permission'])
                    <li class="nav-item">
                        @include('partials.modal-form-btn', [
                            'option' => $option['option'],
                            'id' => $option['id']
                        ])
                    </li>
                @endcan
            @else
                <li class="nav-item">
                    @include('partials.modal-form-btn', [
                        'option' => $option['option'],
                        'id' => $option['id']
                    ])
                </li>
            @endif
            @break

        @case('post')
            @if (isset($option['permission']))
                @can($option['permission'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ $option['url'] }}" onclick="event.preventDefault(); document.getElementById('post-form').submit();">
                            {{ $option['option'] }}
                        </a>

                        <form id="post-form" action="{{ $option['url'] }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                @endcan
            @else
                <li class="nav-item">
                    <a class="nav-link" href="{{ $option['url'] }}" onclick="event.preventDefault(); document.getElementById('post-form').submit();">
                        {{ $option['option'] }}
                    </a>

                    <form id="post-form" action="{{ $option['url'] }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
            @endif
            @break

        @case('hideable')
            @if (isset($option['permission']))
                @can($option['permission'])
                    @if($option['show'])
                        <li class="nav-item">
                            <a class="nav-link" href="{{ $option['url'] }}"
                            {{ isset($option['id']) ? 'id=' . $option['id'] : '' }}
                            {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>
                                {{ $option['option'] }}
                            </a>
                        </li>
                    @endif
                @endcan
            @else
                @if($option['show'])
                    <li class="nav-item">
                        <a class="nav-link" href="{{ $option['url'] }}"
                        {{ isset($option['id']) ? 'id=' . $option['id'] : '' }}
                        {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>
                            {{ $option['option'] }}
                        </a>
                    </li>
                @endif
            @endif
            @break

        @case('dropdown')
            @if (isset($option['permission']))
                @can($option['permission'])
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ $option['option'] }} <i class="fa fa-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            @foreach($option['url'] as $suboption)
                                @include('partials.li-dropdown', ['option' => $suboption])
                            @endforeach
                        </div>
                    </li>
                @endcan
            @else
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ $option['option'] }} <i class="fa fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        @foreach($option['url'] as $suboption)
                            @include('partials.li-dropdown', ['option' => $suboption])
                        @endforeach
                    </div>
                </li>
            @endif
            @break

        @default
            @if (isset($option['permission']))
                @can($option['permission'])
                    <li class="nav-item">
                        <a class="nav-link {{ isset($option['active']) ? 'active btn btn-secondary text-white' : '' }}" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
                    </li>
                @endcan
            @else
                <li class="nav-item">
                    <a class="nav-link {{ isset($option['active']) ? 'active btn btn-secondary text-white' : '' }}" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
                </li>
            @endif
    @endswitch
@else
    @if (isset($option['permission']))
        @can($option['permission'])
            <li class="nav-item">
                <a class="nav-link {{ isset($option['active']) ? 'active btn btn-secondary text-white' : '' }}" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
            </li>
        @endcan
    @else
        <li class="nav-item">
            <a class="nav-link {{ isset($option['active']) ? 'active btn btn-secondary text-white' : '' }}" href="{{ $option['url'] }}" {{ isset($option['id']) ? 'id=' . $option['id'] : '' }} {{ isset($option['target']) ? 'target=' . $option['target'] : '' }}>{{ $option['option'] }}</a>
        </li>
    @endif
@endif