@if(isset($option['type']))
    @switch($option['type'])
        @case('divider')
            <div class="dropdown-divider"></div>
            @break

        @case('confirm')
            @if (isset($option['permission']))
                @can($option['permission'])
                    @include('partials.options.items.confirm')
                @endcan
            @else
                @include('partials.options.items.confirm')
            @endif
            @break

        @case('modal')
            @if (isset($option['permission']))
                @can($option['permission'])
                    @include('partials.options.items.modal')
                @endcan
            @else
                @include('partials.options.items.modal')
            @endif
            @break

        @case('post')
            @if (isset($option['permission']))
                @can($option['permission'])
                    @include('partials.options.items.post')
                @endcan
            @else
                @include('partials.options.items.post')
            @endif
            @break

        @case('hideable')
            @if (isset($option['permission']))
                @can($option['permission'])
                    @if($option['show'])
                        @include('partials.options.items.hideable')
                    @endif
                @endcan
            @else
                @if($option['show'])
                    @include('partials.options.items.hideable')
                @endif
            @endif
            @break

        @default
            @if (isset($option['permission']))
                @can($option['permission'])
                    @include('partials.options.items.default')
                @endcan
            @else
                @include('partials.options.items.default')
            @endif
    @endswitch
@else
    @if (isset($option['permission']))
        @can($option['permission'])
            @include('partials.options.items.default')
        @endcan
    @else
        @include('partials.options.items.default')
    @endif
@endif
