<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                <a href="{{ route('shifts.show', ['id' => Hashids::encode($row->id)]) }}" class="btn btn-link">
                    {{ $row->hotel->business_name }}
                </a>
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->created_at }}
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->closed_at ?? '-' }}
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ number_format($row->cash, 2, ',', '.') }}
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center text-primary">
            <p>
                {{ $row->team_member_name }}
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            @can(['shifts.show'])
                <a href="{{ route('shifts.show', ['id' => Hashids::encode($row->id)]) }}" class="btn btn-link">
                    <i class="fas fa-eye"></i>
                </a>

                <a href="{{ route('shifts.export', ['id' => Hashids::encode($row->id)]) }}" class="btn btn-link" target="_blank">
                    <i class="fas fa-arrow-circle-down"></i>
                </a>
            @endcan
        </div>
    </div>
</div>
