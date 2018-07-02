<div class="hide">
	<div class="modal fade modal-slide-in-right" id="modal-delete" tabindex="-1" role="dialog" aria-labelledby="resource-delete" aria-hidden="true">
		<form action="{url}" method="POST">
			@csrf 
			@method('DELETE')

			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
							&times;
						</button>
						<h4 class="modal-title" id="resource-delete">
							@lang('common.attention')
						</h4>
					</div>
					<div class="modal-body">
						<p>
							@lang('common.confirm')
						</p> 
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-link" data-dismiss="modal">
							@lang('common.cancel')
						</button>
						<button type="Submit" class="btn btn-primary">
							@lang('common.delete')
						</button>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</form>
	</div><!-- /.modal -->
</div>