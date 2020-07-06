<div class="row my-4">
    <div class="col-12">
        <div class="row">
            <div class="col-12 mt-2">
                <p class="text-muted border-top">
                    <small>{{ trans('common.created.at') }}: {{ $note->created_at }}</small>
                </p>
            </div>
            <div class="col-12">
                <p>
                    {!! $note->content !!}
                </p>
            </div>
            <div class="col-12">
                <span>
                    <b>{{ $note->team_member_name }}</b>
                </span>
                <span class="d-block">
                    <small>{{ $note->team_member_email }}</small>
                </span>
            </div>
            <div class="col-12 mt-2">
                <span><b>Tags</b>:</span>
                @foreach ($note->tags as $tag)
                    <a href="{{ route('tags.show', ['id' => id_encode($tag->id), 'hotel' => id_encode($hotel->id)]) }}">#{{ $tag->slug }}</a>
                @endforeach
            </div>
        </div>
    </div>
</div>