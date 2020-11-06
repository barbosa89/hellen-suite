<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th scope="col" class="border-top-0">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                @include($row, ['row' => $item])
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}">
                        @lang('common.noRecords').
                    </td>
                </tr>
            @endforelse

        </tbody>
    </table>
</div>
