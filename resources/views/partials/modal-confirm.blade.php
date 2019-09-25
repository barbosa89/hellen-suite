<div class="hide">
	<div class="modal fade modal-slide-in-right" id="modal-confirm" tabindex="-1" role="dialog" aria-labelledby="confirm-action" aria-hidden="true">
		<form action="{url}" method="POST">
			{{ csrf_field() }}
			<input type="hidden" name="_method" value="{method}">

			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Atención</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<p>
							Por favor confirma esta acción
						</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-link" data-dismiss="modal">
							Cancelar
						</button>
						<button type="Submit" class="btn btn-primary">
							Continuar
						</button>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</form>
	</div><!-- /.modal -->
</div>