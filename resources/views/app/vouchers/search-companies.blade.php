@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('voucher', $voucher) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('vouchers.title'),
            'url' => route('vouchers.index'),
            'options' => [
                [
                    'option' => trans('common.create') . ' ' . trans('companies.company'),
                    'url' => route('vouchers.companies.create', [
                        'id' => id_encode($voucher->id)
                    ])
                ],
                [
                    'option' => trans('vouchers.see'),
                    'url' => route('vouchers.show', [
                        'id' => id_encode($voucher->id)
                    ])
                ]
            ]
        ])

        @include('app.vouchers.info')

        <div class="hide" id="voucher" data-id="{{ id_encode($voucher->id) }}"></div>

        @include('partials.spacer', ['size' => 'md'])

        <div class="row">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('common.search') . ' ' . trans('companies.title'),
                        'align' => 'text-center',
                        'size' => 'h3'
                    ],
                    'url' => '#',
                    'fields' => [
                        'app.vouchers.search-field',
                    ],
                    'csrf' => false
                ])
            </div>
        </div>

        <div class="crud-list" id="list" style="display:none;">
            <div class="crud-list-heading">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <h5>@lang('common.name')</h5>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <h5>@lang('companies.tin')</h5>
                    </div>
                </div>
            </div>
            <div class="crud-list-items" id="item-search">

            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection

@section('scripts')
<script type="text/javascript">
import { toast } from 'vue3-toastify'
import { trans } from 'laravel-vue-i18n'

function search(str) {
    const url = '{{ url('companies/search') }}';
    const uri = `?query=${str}&format=rendered&template=vouchers`;

    if (str.length === 0) {
        document.querySelector('#list').style.display = 'none';
        document.querySelector('#item-search').innerHTML = '';
    }

    if (str.length >= 3) {
        axios.get(url + uri, {
            params: { query: str }
        })
        .then(response => {
            const companies = response.data.companies;

            if (companies.length) {
                const itemSearch = document.querySelector('#item-search');
                itemSearch.innerHTML = '';

                companies.forEach(company => {
                    const item = `
                        <a href="#" onclick="add(this, event)" data-value="${company.hash}">
                            <div class="crud-list-row">
                                <div class="row">
                                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                        <p>${company.business_name}</p>
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                        <p>${company.tin}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    `;
                    itemSearch.insertAdjacentHTML('beforeend', item);
                });

                document.querySelector('#list').style.display = 'block';
            }
        })
        .catch(error => {
            toast.error('Ha ocurrido un error'); // TODO: Add translation
        });
    }
}

function add(el, e) {
    e.preventDefault()

    const voucher = document.querySelector('#voucher').getAttribute('data-id');
    const company = el.dataset.value
    const url = '/vouchers/'+ voucher +'/companies/' + company

    window.location.replace(url)
}
</script>
@endsection