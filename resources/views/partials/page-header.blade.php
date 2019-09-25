@include('partials.menu', [
    'options' => $options,
    'title' => $title,
    'url' => $url,
    'search' => isset($search) ? $search : null
])

<div class="mt-4">
    @include('flash::message')
</div>
