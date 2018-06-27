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
                @if(is_string($option['url']))
                    @if(isset($option['active']) and $option['active'] == true)
                        <li class="active"><a href="{{ $option['url'] }}">{{ $option['option'] }}</a></li>
                    @else
                        <li><a href="{{ $option['url'] }}">{{ $option['option'] }}</a></li>
                    @endif
                @else
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            {{ $option['option'] }} <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @foreach($option['url'] as $suboption)
                                <li><a href="{{ $suboption['url'] }}">{{ $suboption['option'] }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
  </nav> 