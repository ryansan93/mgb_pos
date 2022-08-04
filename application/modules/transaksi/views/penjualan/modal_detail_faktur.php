<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">DETAIL FAKTUR</label></span>
</div>
<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-xs-12" style="padding-top: 10px;">
			<div class="col-xs-3 no-padding"><label class="label-control">No. Faktur</label></div>
			<div class="col-xs-9 no-padding"><label class="label-control">: <?php echo $data['kode_faktur']; ?></label></div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 no-padding"><label class="label-control">Tanggal</label></div>
			<div class="col-xs-9 no-padding"><label class="label-control">: <?php echo $data['tgl_trans']; ?></label></div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 no-padding"><label class="label-control">Kasir</label></div>
			<div class="col-xs-9 no-padding"><label class="label-control">: <?php echo $data['nama_kasir']; ?></label></div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-3 no-padding"><label class="label-control">Pelanggan</label></div>
			<div class="col-xs-9 no-padding"><label class="label-control">: <?php echo $data['member']; ?></label></div>
		</div>
		<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-xs-12">
			<table class="table table-bordered tbl_item" style="margin-bottom: 10px;">
				<thead>
					<tr>
						<th class="col-xs-2">Jenis Pesanan</th>
						<th class="col-xs-4">Menu</th>
						<th class="col-xs-2">Jumlah</th>
						<th class="col-xs-2">Harga</th>
						<th class="col-xs-2">Total</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( !empty($data['jual_item']) ): ?>
						<?php foreach ($data['jual_item'] as $k_ji => $v_ji): ?>
							<tr>
								<td><?php echo $v_ji['jenis_pesanan'][0]['nama']; ?></td>
								<td>
									<div class="col-xs-12 no-padding"><?php echo $v_ji['menu_nama']; ?></div>
									<?php if ( !empty($v_ji['jual_item_detail']) ): ?>
										<?php foreach ($v_ji['jual_item_detail'] as $k_jid => $v_jid): ?>
											<div class="col-xs-12 no-padding" style="padding-left: 20px; font-size: 10px;"><?php echo $v_jid['menu_nama']; ?></div>
										<?php endforeach ?>
									<?php endif ?>
								</td>
								<td class="text-right"><?php echo angkaRibuan($v_ji['jumlah']); ?></td>
								<td class="text-right"><?php echo angkaDecimal($v_ji['harga']); ?></td>
								<td class="text-right"><?php echo angkaDecimal($v_ji['total']); ?></td>
							</tr>
						<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="4">Data tidak ditemukan.</td>
						</tr>
					<?php endif ?>
				</tbody>
			</table>
		</div>
		<?php if ( !empty($data['jual_diskon']) ): ?>
			<div class="col-xs-12">
				<table class="table table-bordered tbl_diskon" style="margin-bottom: 10px;">
					<thead>
						<tr>
							<th class="col-xs-12">Nama Diskon</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($data['jual_diskon'] as $k_jd => $v_jd): ?>
							<tr>
								<td><?php echo $v_jd['diskon_nama']; ?></td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>
		<?php endif ?>
		<div class="col-xs-12"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
		<div class="col-xs-12">
			<div class="col-xs-9 no-padding text-right"><label class="label-control">Sub Total. :</label></div>
			<div class="col-xs-3 no-padding text-right"><label class="label-control"><?php echo angkaDecimal($data['total']); ?></label></div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-9 no-padding text-right"><label class="label-control">Diskon. :</label></div>
			<div class="col-xs-3 no-padding text-right"><label class="label-control"><?php echo angkaDecimal($data['diskon']); ?></label></div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-9 no-padding text-right"><label class="label-control">Total Bayar. :</label></div>
			<div class="col-xs-3 no-padding text-right"><label class="label-control"><?php echo angkaDecimal($data['grand_total']); ?></label></div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-9 no-padding text-right"><label class="label-control">Uang Tunai. :</label></div>
			<div class="col-xs-3 no-padding text-right"><label class="label-control"><?php echo angkaDecimal($data['bayar'][0]['jml_bayar']); ?></label></div>
		</div>
		<div class="col-xs-12">
			<div class="col-xs-9 no-padding text-right"><label class="label-control">Kembalian. :</label></div>
			<div class="col-xs-3 no-padding text-right"><label class="label-control"><?php echo angkaDecimal(($data['bayar'][0]['jml_bayar'] - $data['grand_total'])); ?></label></div>
		</div>
		<div class="col-xs-12" style="padding-top: 10px;">
			<div class="col-xs-6 no-padding" style="padding-right: 5px;">
				<div class="col-md-12 text-center cursor-p btn-cancel button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
					<span><b><i class="fa fa-long-arrow-left"></i></b></span>
				</div>
			</div>
			<div class="col-xs-6 no-padding" style="padding-left: 5px;">
				<div class="col-md-12 text-center cursor-p btn-ok button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
					<span><b><i class="fa fa-print"></i></b></span>
				</div>
			</div>
		</div>
	</div>
</div>