<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ trans('notes.title') }}</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>@lang('common.date')</th>
                <th>@lang('notes.content')</th>
                <th>@lang('notes.author')</th>
                <th>Tags</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($shift->notes as $note)
                <tr>
                    <td>{{ $note->created_at }}</td>
                    <td>
                        {!! $note->content !!}
                    </td>
                    <td>
                        {{ $note->team_member_name }} - {{ $note->team_member_email }}
                    </td>
                    <td>
                        @foreach ($note->tags as $tag)
                            <a href="{{ route('tags.show', ['id' => id_encode($tag->id), 'hotel' => id_encode($shift->hotel->id)]) }}">#{{ $tag->slug }}</a>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>