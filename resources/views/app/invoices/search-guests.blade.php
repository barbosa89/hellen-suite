@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('invoices.title'),
            'url' => route('invoices.index'),
            'options' => [
                [
                    'option' => trans('common.new') . ' ' . trans('guests.guest'),
                    'url' => route('invoices.guests.create', ['id' => Hashids::encode($invoice->id)])
                ],
                [
                    'option' => trans('common.seeMore'),
                    'url' => '#'
                ],
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        @include('app.invoices.info')

        @include('partials.spacer', ['size' => 'md'])

        <div class="row">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('common.search') . ' ' . trans('guests.title'),
                        'align' => 'text-center',
                        'size' => 'h3'
                    ],
                    'url' => '#',
                    'fields' => [
                        'app.invoices.search-field',
                    ],
                    'csrf' => false
                ])
            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        function searchGuests (str) {
            const url = '{{ url('guests/search') }}';
            const uri = "?query=" + str + "&format=rendered&template=invoices";

            if (str.length == 0) {
                alert('Cero');
            }

            if (str.length >= 3) {
                $.get(url + uri, function (data, status) {
                    console.log(data);
                });
            }
        }
    </script>
@endsection