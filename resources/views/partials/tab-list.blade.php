<ul id="myTab" class="nav nav-tabs" role="tablist">
    @foreach($tabs as $tab)
        @if($loop->first)
            <li class="nav-item">
                <a class="nav-link active" id="{{ $tab['id'] }}-tab" href="#{{ $tab['id'] }}" data-toggle="tab" aria-controls="{{ $tab['id'] }}" aria-selected="true">
                    {{ $tab['title'] }}
                </a>
            </li>
        @else
            <li class="nav-item">
                <a class="nav-link" id="{{ $tab['id'] }}-tab" href="#{{ $tab['id'] }}" data-toggle="tab" aria-controls="{{ $tab['id'] }}" aria-selected="false">
                    {{ $tab['title'] }}
                </a>
            </li>
        @endif
    @endforeach
</ul>
<div id="myTabContent" class="tab-content">
    @foreach($tabs as $tab)
        @if($loop->first)
            <div class="tab-pane fade show active" id="{{ $tab['id'] }}" role="tabpanel" aria-labelledby="{{ $tab['id'] }}-tab">
                @include('partials.list', [
                    'data' => $data,
                    'listHeading' => $listHeading,
                    'listRow' => $listRow,
                    'where' => isset($tab['where']) ? $tab['where'] : null,
                ])
            </div>
        @else
            <div class="tab-pane fade in" id="{{ $tab['id'] }}" role="tabpanel" aria-labelledby="{{ $tab['id'] }}-tab">
                @include('partials.list', [
                    'data' => $data,
                    'listHeading' => $listHeading,
                    'listRow' => $listRow,
                    'where' => isset($tab['where']) ? $tab['where'] : null,
                ])
            </div>
        @endif
    @endforeach
</div>