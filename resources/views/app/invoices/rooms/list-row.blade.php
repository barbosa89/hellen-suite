<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2"> 
            <div class="pretty p-icon p-rotate">
                <input type="checkbox" name="barcode_{{ $row->id }}" value="{{ $row->id }}" />
                <div class="state p-primary">
                    <i class="icon fa fa-check"></i>
                    <label>{{ $row->number }}</label>
                </div>
            </div>
            
            <a href="{{ route('rooms.show', ['room' => Hashids::encode($row->id)]) }}">
                <i class="fa fa-plus-circle"></i>
            </a>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 visible-md visible-lg">
            <p><a href="{{ route('rooms.show', ['room' => Hashids::encode($row->id)]) }}">{{ number_format($row->value, 2, ',', '.') }}</a></p>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3 visible-md visible-lg">
            <p>
                <a href="#" data-toggle="tooltip" title="{{ $row->description }}">
                    {{ str_limit($row->description, 30) }}
                </a>
            </p>            
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">

        </div>
    </div>
</div>