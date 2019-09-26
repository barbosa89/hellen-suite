<div class="dropdown">
    <button class="btn btn-link" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-chevron-down"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
        @foreach($options as $option)
            @include('partials.li-dropdown', ['option' => $option])
        @endforeach
    </div>
</div>