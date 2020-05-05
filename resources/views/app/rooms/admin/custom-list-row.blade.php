<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
            <p>
                <a href="{{ route('rooms.show', ['id' => id_encode($row->id)]) }}">
                    {{ $row->number }}

                    @if ($row->is_suite)
                        <i class="fa fa-star"></i>
                    @endif
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
            <p>
                {{ $row->capacity }}
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 visible-md visible-lg">
            <p>
                <a href="{{ route('rooms.show', ['id' => id_encode($row->id)]) }}">
                    {{ number_format($row->price, 2, ',', '.') }}
                </a>
            </p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 visible-md visible-lg">
            <p>
                <a href="{{ route('rooms.show', ['id' => id_encode($row->id)]) }}">
                    {{ number_format($row->min_price, 2, ',', '.') }}
                </a>
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
            @include('app.rooms.admin.item-menu')
        </div>
    </div>
</div>
