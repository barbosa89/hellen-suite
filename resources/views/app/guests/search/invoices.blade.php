<a href="#" onclick="addGuest(this, event)" data-guest="{{ Hashids::encode($guest->id) }}">
    <div class="crud-list-row">
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                <p>{{ $guest->name . ' ' . $guest->last_name }}</p>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                <p>{{ $guest->dni }}</p>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 visible-md visible-lg">
                <p>
                    @include('partials.guest-status', ['status' => $guest->status])
                </p>
            </div>
        </div>
    </div>
</a>