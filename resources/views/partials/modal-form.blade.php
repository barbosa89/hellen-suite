<div class="modal fade modal-slide-in-right" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
	<form action="{{ $action }}" method="POST">
		@csrf 
		<input type="hidden" name="_method" value="{{ $method or 'PUT' }}">

		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						&times;
					</button>
					<h4 class="modal-title" id="modal-form">
						@lang('common.attention')
					</h4>
				</div>
				<div class="modal-body">
					@include($form)
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