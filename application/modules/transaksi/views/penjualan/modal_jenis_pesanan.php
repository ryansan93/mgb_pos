<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">Pilih Jenis Pesanan</label></span>
</div>
<div class="modal-body body no-padding">
	<div class="row">
		<?php 
			$jml_row = ceil(count($jenis_pesanan) / 3);
			$j = 0;
			for ($i=0; $i < $jml_row; $i++) { ?>
				<div class="col-lg-12" style="padding-top: 5px; display: flex; align-items: center; justify-content: center;">
					<?php $_j = $j+3; ?>
					<?php for (;$j < $_j; $j++) { ?>		
						<?php if ( isset($jenis_pesanan[$j]) ): ?>
							<div class="col-md-4 no-padding" style="height: 100px; padding-right: 1%; padding-bottom: 5px;">
								<div class="col-md-12 text-center cursor-p button" style="height: 100%; display: flex; justify-content: center; align-items: center;" data-kode="<?php echo $jenis_pesanan[$j]['kode']; ?>">
									<span><b><?php echo $jenis_pesanan[$j]['nama']; ?></b></span>
								</div>
							</div>
						<?php endif ?>				
					<?php } ?>
				</div>
		<?php } ?>
		<div class="col-md-12" style="height: 100px; padding-top: 2%;">
			<div class="col-md-12 text-center cursor-p btn-exit button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
				<span><b>KELUAR</b></span>
			</div>
		</div>
	</div>
</div>