@if(isset($option['type']))
    @switch($option['type'])
        @case('divider')
            <div class="dropdown-divider"></div>
            @break

        @case('confirm')
            @if (isset($option['permission']))
                @can($option['permission'])
                    @include('partials.options.confirm')
                @endcan
            @else
                @include('partials.options.confirm')
            @endif
            @break

        @case('modal')
            @if (isset($option['permission']))
                @can($option['permission'])
                    @include('partials.options.modal')
                @endcan
            @else
                @include('partials.options.modal')
            @endif
            @break

        @case('post')
            {{ dd($option['option']) }}
            @if (isset($option['permission']))
                @can($option['permission'])
                    @include('partials.options.post')
                @endcan
            @else
                @include('partials.options.post')
            @endif
            @break

        @case('hideable')
            @if (isset($option['permission']))
                @can($option['permission'])
                    @if($option['show'])
                        @include('partials.options.hideable')
                    @endif
                @endcan
            @else
                @if($option['show'])
                    @include('partials.options.hideable')
                @endif
            @endif
            @break

        @case('dropdown')
            @if (isset($option['permission']))
                @can($option['permission'])
                    @include('partials.options.dropdown')
                @endcan
            @else
                @include('partials.options.dropdown')
            @endif
            @break

        @default
            @if (isset($option['permission']))
                @can($option['permission'])
                    @include('partials.options.default')
                @endcan
            @else
                @include('partials.options.default')
            @endif
    @endswitch
@else
    @if (isset($option['permission']))
        @can($option['permission'])
            @include('partials.options.default')
        @endcan
    @else
        @include('partials.options.default')
    @endif
@endif
