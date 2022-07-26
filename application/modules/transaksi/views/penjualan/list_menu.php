<?php if ( !empty($data) ): ?>
	<?php foreach ($data as $k_data => $v_data): ?>
		<div class="col-sm-4 no-padding menu cursor-p" data-kode="<?php echo $v_data['kode_menu']; ?>" data-jmlpaket="<?php echo $v_data['jml_paket']; ?>" onclick="jual.cekPaket(this)">
			<div class="col-sm-12 border">
				<div class="col-sm-12 no-padding">
					<div class="col-sm-6 no-padding nama_menu"><?php echo $v_data['nama']; ?></div>
					<div class="col-sm-6 no-padding text-right harga_menu"><?php echo angkaDecimal($v_data['harga_jual']); ?></div>
				</div>
				<br>
				<div class="col-sm-12" style="padding: 5px 0px 5px 0px; font-size: 10px;"><?php echo $v_data['deskripsi']; ?></div>
			</div>
		</div>
	<?php endforeach ?>
<?php endif ?>