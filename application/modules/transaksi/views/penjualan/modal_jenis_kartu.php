<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">PILIH KARTU</label></span>
	<button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button>
</div>
<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-lg-12" style="padding-top: 10px;">
			<?php if ( !empty($data) ): ?>
				<?php foreach ($data as $key => $value): ?>
					<div class="col-md-4 no-padding" style="height: 100px; padding-right: 1%; padding-bottom: 1%;">
						<div class="col-md-12 text-center cursor-p btn-jenis-kartu button" style="height: 100%; display: flex; justify-content: center; align-items: center;" data-kode="<?php echo $value['kode_jenis_kartu']; ?>">
							<span><b><?php echo strtoupper($value['nama']); ?></b></span>
						</div>
					</div>
				<?php endforeach ?>
			<?php else: ?>
				<div class="col-md-12 no-padding" style="height: 100px; padding-right: 1%;">
					Harap hubungi admin untuk input kartu
				</div>
			<?php endif ?>
		</div>
	</div>
</div>