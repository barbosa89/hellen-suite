<div class="row">
    <div class="content-header">
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            <a href="{{ $url }}"><h1 class="page-header">{{ $title }}</h1></a>
        </div>
        <div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
            @if(count($options) > 0)
                @include('partials.menu', ['options' => $options])
            @endif
        </div>
    </div>
</div>
<div class="row">
    @include('flash::message')
</div>