<div class="card">
    <div class="card-body">
        <h5 class="card-title">
            {{ trans('plans.type.free') }}
        </h5>
        <h6 class="card-subtitle mb-2 text-muted mh-card-subtitle">
            {{ trans('plans.descriptions.free', ['months' => $plan->months]) }}
        </h6>
        <p class="card-text">
            <ul class="list-unstyled">
                @include('app.plans.types.features.free')
            </ul>
        </p>
        <a href="{{ route('plans.buy', ['id' => id_encode($plan->id)]) }}" class="btn btn-primary card-link">
            @lang('common.select')
        </a>
    </div>
</div>
