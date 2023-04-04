<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">HELP</label></span>
	<button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button>
</div>
<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-xs-12" style="padding-top: 10px;">
			<div class="col-xs-6 no-padding" style="height: 100px; padding-right: 1%;">
				<div class="col-xs-12 text-center cursor-p btn-tesprint button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
					<span><b>TES PRINT</b></span>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
	</div>
	<?php if ( hasAkses('transaksi/ListShift') ): ?>
		<div class="row">
			<div class="col-xs-12">
				<button type="button" class="btn btn-primary" onclick="window.open('transaksi/ListShift', 'blank')"><i class="fa fa-list"></i> List Shift</button>
			</div>
		</div>
	<?php endif ?>
</div>