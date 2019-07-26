<div class="dropdown">
    <button class="btn btn-link dropdown-toggle w-btn-dropdown" id="dropdownMenuButton" type="button" data-toggle="dropdown">
        <span class="fa fa-caret-down fa-2x"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        @foreach($options as $option)
            @include('partials.li', ['option' => $option])
        @endforeach
    </ul>
</div>