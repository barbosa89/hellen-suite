<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="{{ $url }}">
        {{ $title }}
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            @foreach($options as $option)
                @include('partials.li', ['option' => $option])
            @endforeach
        </ul>

        @if (!empty($search))
            <form class="form-inline my-2 my-lg-0" action="{{ $search['action'] }}" method="get">
                <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search" {{ isset($search['query']) ? 'value=' . $search['query'] : '' }}>
                <button class="btn btn-primary my-2 my-sm-0" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </form>
        @endif
    </div>
</nav>