<div class="card">
    <div class="card-body">
        <h5 class="card-title">
            {{ trans('plans.type.basic') }}
        </h5>
        <h6 class="card-subtitle mb-2 text-muted mh-card-subtitle">
            {{ trans('plans.descriptions.basic') }}
        </h6>
        <p class="card-text">
            <ul class="list-unstyled">
                @include('app.plans.types.features.basic')
            </ul>
        </p>
        <a href="#" class="btn btn-primary card-link"
            onclick="event.preventDefault(); document.getElementById('buy-plan-{{ id_encode($plan->id) }}').submit();">
            @lang('common.select')
        </a>

        <form id="buy-plan-{{ id_encode($plan->id) }}" action="{{ route('plans.buy') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="plan_id" value="{{ id_encode($plan->id) }}">
        </form>
    </div>
</div>
