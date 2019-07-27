<div class="modal fade modal-slide-in-right" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
	<form action="{{ $action }}" method="POST">
		@csrf

		@if(isset($method) and !empty($method))
			<input type="hidden" name="_method" value="{{ $method }}">
		@endif

		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{ $title }}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="modal-fields">
						@if(!empty($fields))
							@if(is_array($fields))
								@foreach($fields as $field)
									@include($field)
								@endforeach
							@else
								@include($fields)
							@endif
						@endif
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-link" data-dismiss="modal">
						@lang('common.cancel')
					</button>
					<button type="Submit" class="btn btn-primary">
						@lang('common.continue')
					</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</form>
</div><!-- /.modal -->