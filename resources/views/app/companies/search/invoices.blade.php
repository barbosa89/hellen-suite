<a href="#" onclick="add(this, event)" data-value="{{ id_encode($value->id) }}">
    <div class="crud-list-row">
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <p>{{ $value->business_name }}</p>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                <p>{{ $value->tin }}</p>
            </div>
        </div>
    </div>
</a>
