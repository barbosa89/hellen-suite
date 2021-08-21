<nav class="navbar navbar-expand-lg navbar-light app-nav">
    <a class="navbar-brand text-muted" href="{{ $url }}">
        {{ $title }}
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        @if ($search)
            <form class="form-inline my-2 my-lg-0" action="{{ $url }}" method="get">
                <div class="input-group">
                    <input class="form-control" type="search" name="search" placeholder="{{ trans('common.search') }}" aria-label="Search" value="{{ request()->input('search') }}" required>
                    <div class="input-group-append">
                        <button type="submit" class="input-group-text" id="btnGroupAddon">
                            <em class="fa fa-search"></em>
                        </button>
                        @if (!empty(request()->input('search')))
                            <a href="{{ $url }}" class="btn input-group-text">
                                <em class="fa fa-redo"></em>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        @endif

        <ul class="navbar-nav ml-auto">
            {{ $slot }}
        </ul>
    </div>
</nav>

<div class="mt-3">
    @include('flash::message')
</div>
