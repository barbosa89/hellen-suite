<div class="card">
    <div class="card-body">
        <h5 class="card-title">
            {{ trans('plans.type.default') }}
        </h5>
        <h6 class="card-subtitle mb-2 text-muted mh-card-subtitle">
            {{ trans('plans.descriptions.default') }}
        </h6>
        <p class="card-text">
            <ul class="list-unstyled">
                @include('app.plans.types.features.default')
            </ul>
        </p>
        <a class="btn btn-primary card-link" href="tel:+57{{ config('settings.tel') }}">
            @lang('landing.contact')
        </a>
    </div>
</div>
