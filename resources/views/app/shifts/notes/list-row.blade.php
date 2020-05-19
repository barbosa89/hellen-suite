<div class="crud-list-row">
    <div class="row">
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center dont-break-out">
            <p>
                {{ $row->created_at }}
            </p>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 align-self-center">
            <p>
                {!! $row->content !!}
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
            <p>
                {{ $row->team_member_name }} <br>
                <small>{{ $row->team_member_email }}</small>
            </p>
        </div>
        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center dont-break-out">
            <p>
                @foreach ($row->tags as $tag)
                    <a href="{{ route('tags.show', ['id' => id_encode($tag->id), 'hotel' => id_encode($shift->hotel->id)]) }}">#{{ $tag->slug }}</a>
                @endforeach
            </p>
        </div>
    </div>
</div>