<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2"> 
            <p>{{ $row->number }}</p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
            <p>{{ number_format($row->value, 2, ',', '.') }}</p>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 visible-md visible-lg">
            <p>
                <a href="#" data-toggle="tooltip" title="{{ $row->description }}">
                    {{ str_limit($row->description, 30) }}
                </a>
            </p>            
        </div>
    </div>
</div>