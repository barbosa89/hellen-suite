@if(!isset($where))
    @if($data->count() > 0)
        <div class="crud-list">
            <div class="crud-list-heading mt-2">
                @include($listHeading)
            </div>
            <div class="crud-list-items">
                @foreach($data as $row)
                    @include($listRow, ['row' => $row])
                @endforeach
            </div>
        </div>

        @if($data->count() >= 0 and method_exists($data, 'render'))
            <div class="row">
                <div class="col-md-12">
                    <div class="text-center">
                        {{ $data->render() }}
                    </div>
                </div>
            </div>
        @endif
    @else
        @include('partials.no-records')
    @endif
@else
    @if($data->whereIn($where['field'], $where['values'])->count() > 0)
        <div class="crud-list">
            <div class="crud-list-heading mt-2">
                @include($listHeading)
            </div>
            <div class="crud-list-items">
                @foreach($data->whereIn($where['field'], $where['values']) as $row)
                    @include($listRow, ['row' => $row])
                @endforeach
            </div>
        </div>
    @else
        @include('partials.no-records')
    @endif
@endif