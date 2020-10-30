<ul class="list-unstyled mt-3 mb-4">
    @include('app.plans.types.features.sponsor')
</ul>

<div class="log-in mt-md-3 mt-2">
    <a class="btn" href="mailto:{{ config('mail.from.address') }}">
        @lang('landing.contact')
    </a>
</div>
