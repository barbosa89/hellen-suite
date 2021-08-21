@if ($guests->isNotEmpty())
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">@lang('common.name')</th>
                    <th scope="col">@lang('common.idNumber')</th>
                    <th scope="col">@lang('common.status')</th>
                    <th scope="col">@lang('common.options')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($guests as $guest)
                    <tr>
                        <td>
                            @can('guests.show')
                                <a href="{{ route('guests.show', ['id' => $guest->hash]) }}">
                                    {{ $guest->full_name }}
                                </a>
                            @endcan
                        </td>
                        <td>
                            @can('guests.show')
                                <a href="{{ route('guests.show', ['id' => $guest->hash]) }}">
                                    {{ $guest->dni }}
                                </a>
                            @endcan
                        </td>
                        <td>
                            @include('partials.guest-status', ['status' => $guest->status])
                        </td>
                        <td>
                            @include('partials.dropdown-btn', [
                                'options' => [
                                    [
                                        'option' => trans('common.show'),
                                        'url' => route('guests.show', ['id' => $guest->hash]),
                                        'permission' => 'guests.show'
                                    ],
                                    [
                                        'option' => trans('common.edit'),
                                        'url' => route('guests.edit', [
                                            'id' => $guest->hash
                                        ]),
                                        'permission' => 'guests.edit'
                                    ],
                                    [
                                        'type' => 'confirm',
                                        'option' => trans('common.delete.item'),
                                        'url' => route('guests.destroy', [
                                            'id' => $guest->hash
                                        ]),
                                        'method' => 'DELETE',
                                        'permission' => 'guests.destroy'
                                    ],
                                ]
                            ])
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <tr>
        <td colspan="4">@lang('common.noRecords')</td>
    </tr>
@endif
