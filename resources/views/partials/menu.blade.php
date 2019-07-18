<nav class="navbar navbar-default normalize-menu">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav pull-right">
            @foreach($options as $option)
                @include('partials.li', ['option' => $option])
            @endforeach
        </ul>
    </div>
</nav>