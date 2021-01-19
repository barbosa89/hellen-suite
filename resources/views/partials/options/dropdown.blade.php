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
