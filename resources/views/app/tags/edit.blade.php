@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('tag', $tag) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => 'Tags',
            'url' => route('tags.index'),
            'options' => []
        ])


        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('common.editionOf') Tags</h2>
                <form action="{{ route('tags.update', ['id' => id_encode($tag->id)]) }}" method="POST">
                    @csrf()
                    @method('PUT')

                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        <label for="description">@lang('common.description'):</label>
                        <input type="text" class="form-control" name="description" id="description" value="{{ $tag->description }}" required maxlength="191" placeholder="{{ trans('common.required') }}">

                        @if ($errors->has('description'))
                            <span class="help-block">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">
                        @lang('common.update')
                    </button>
                    <a href="{{ url()->previous() }}" class="btn btn-default">
                        @lang('common.back')
                    </a>
                </form>
            </div>
        </div>
    </div>

@endsection