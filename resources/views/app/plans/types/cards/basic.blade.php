<div class="card">
    <div class="card-body">
        <h5 class="card-title">
            {{ trans('plans.type.basic') }}
        </h5>
        <h6 class="card-subtitle mb-2 text-body-secondary mh-card-subtitle">
            {{ trans('plans.descriptions.basic', ['months' => $plan->months]) }}
        </h6>
        <p class="card-text">
            <ul class="list-unstyled">
                @include('app.plans.types.features.basic')
            </ul>
        </p>
        <a href="{{ route('plans.buy', ['id' => id_encode($plan->id)]) }}" class="btn btn-dark card-link">
            @lang('common.select')
        </a>
    </div>
</div>
