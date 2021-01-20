<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th scope="col">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if ($slot->isEmpty())
                <tr>
                    <td colspan="{{ count($headers) }}">
                        @lang('common.noRecords').
                    </td>
                </tr>
            @else
                {{ $slot }}
            @endif

        </tbody>
    </table>
</div>
