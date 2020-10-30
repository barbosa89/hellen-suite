<div class="card">
    <div class="card-body">
        <h5 class="card-title">
            {{ trans('plans.type.sponsor') }}
        </h5>
        <h6 class="card-subtitle mb-2 text-muted mh-card-subtitle">
            {{ trans('plans.descriptions.sponsor') }}
        </h6>
        <p class="card-text">
            <ul class="list-unstyled">
                @include('app.plans.types.features.sponsor')
            </ul>
        </p>
        <a class="btn btn-primary card-link" href="mailto:{{ config('mail.from.address') }}">
            @lang('landing.contact')
        </a>
    </div>
</div>
