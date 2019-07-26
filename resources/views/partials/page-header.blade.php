@include('partials.menu', [
    'options' => $options,
    'title' => $title,
    'url' => $url,
    'search' => isset($search) ? $search : null
])

<div class="col mt-4">
    @include('flash::message')
</div>
