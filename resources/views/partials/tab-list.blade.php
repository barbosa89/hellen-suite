<ul id="myTab" class="nav nav-tabs">
    @foreach($tabs as $tab)
        @if($loop->first)
            <li class="active">
                <a href="#{{ $tab['id'] }}" data-toggle="tab">
                    {{ $tab['title'] }}
                </a>
            </li>
        @else
            <li>
                <a href="#{{ $tab['id'] }}" data-toggle="tab">
                    {{ $tab['title'] }}
                </a>
            </li>
        @endif
    @endforeach
</ul>
<div id="myTabContent" class="tab-content">
    @foreach($tabs as $tab)
        @if($loop->first)
            <div class="tab-pane fade in active" id="{{ $tab['id'] }}">
                @include('partials.list', [
                    'data' => $data,
                    'listHeading' => 'rooms.list-heading',
                    'listRow' => 'rooms.list-row',
                    'where' => isset($tab['where']) ? $tab['where'] : null,
                ])
            </div>
        @else
            <div class="tab-pane fade in" id="{{ $tab['id'] }}">
                @include('partials.list', [
                    'data' => $data,
                    'listHeading' => 'rooms.list-heading',
                    'listRow' => 'rooms.list-row',
                    'where' => isset($tab['where']) ? $tab['where'] : null,
                ])
            </div>
        @endif
    @endforeach
</div>