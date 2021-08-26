<navigation
    v-cloak
    title="{{ $title }}"
    url="{{ $url }}"
    @if ($hotelSelect) hotel-select @endif>

    {{ $slot }}
</navigation>

<div class="mt-3">
    @include('flash::message')
</div>
