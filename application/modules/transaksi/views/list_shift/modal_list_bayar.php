<div class="modal-header no-padding header" style="">
	<span class="modal-title"><label class="label-control">PEMBAYARAN</label></span>
	<button type="button" class="close" data-dismiss="modal" style="color: #000000;">&times;</button>
</div>
<div class="modal-body body no-padding">
	<div class="row">
		<div class="col-lg-12" style="padding-top: 10px;">
			<label class="label-control"><?php echo strtoupper('Tanggal : '.tglIndonesia(date('Y-m-d'), '-', ' ', TRUE)); ?></label>
		</div>
		<div class="col-lg-12" style="padding-top: 10px;">
			<div class="panel-body no-padding">
				<?php $jml_transaksi = 0; $grand_total = 0; $jml_bayar = 0; $selisih_bayar= 0; ?>
				<div class="col-lg-12 no-padding">
					<div class="col-md-12 search left-inner-addon no-padding" style="margin-bottom: 10px;">
						<i class="fa fa-search"></i><input class="form-control" type="search" data-table="tbl_bayar" placeholder="Search" onkeyup="filter_all(this)">
					</div>
					<small>
						<table class="table table-bordered tbl_bayar" style="margin-bottom: 0px;">
							<thead>
								<tr>
									<th class="col-lg-2">No. Nota</th>
									<th class="col-lg-3">Pelanggan</th>
									<th class="col-lg-2">Total</th>
									<th class="col-lg-2">Total Bayar</th>
									<th class="col-lg-2">Selisih Bayar</th>
								</tr>
							</thead>
							<tbody>
								<?php if ( !empty($data['data_bayar']) ): ?>
									<?php foreach ($data['data_bayar'] as $key => $value): ?>
										<tr class="cursor-p bayar search">
											<td class="kode_faktur"><?php echo $value['kode_faktur']; ?></td>
											<td><?php echo $value['pelanggan']; ?></td>
											<td class="text-right"><?php echo angkaDecimal($value['total']); ?></td>
											<td class="text-right"><?php echo angkaDecimal($value['total_bayar']); ?></td>
											<td class="text-right"><?php echo angkaDecimal($value['selisih_bayar']); ?></td>
										</tr>
										<?php 
											$jml_transaksi++;
											$grand_total += $value['total'];
											$jml_bayar += $value['total_bayar'];
											$selisih_bayar = $jml_bayar - $grand_total;
										?>
									<?php endforeach ?>
								<?php else: ?>
									<tr>
										<td colspan="4">Data tidak ditemukan.</td>
									</tr>
								<?php endif ?>
							</tbody>
						</table>
					</small>
				</div>
				<div class="col-lg-12 no-padding"><hr style="margin-top: 10px; margin-bottom: 10px;"></div>
				<div class="col-lg-12 no-padding">
					<div class="col-lg-12 no-padding">
						<label class="col-lg-3 no-padding">Jumlah Transaksi</label>
						<label class="col-lg-1">:</label>
						<label class="col-lg-8"><?php echo angkaRibuan($jml_transaksi); ?></label>
					</div>
					<div class="col-lg-12 no-padding">
						<label class="col-lg-3 no-padding">Total Transaksi</label>
						<label class="col-lg-1">:</label>
						<label class="col-lg-8"><?php echo angkaDecimal($grand_total); ?></label>
					</div>
					<div class="col-lg-12 no-padding">
						<label class="col-lg-3 no-padding">Total Bayar</label>
						<label class="col-lg-1">:</label>
						<label class="col-lg-8"><?php echo angkaDecimal($jml_bayar); ?></label>
					</div>
					<div class="col-lg-12 no-padding">
						<label class="col-lg-3 no-padding">Selisih Bayar</label>
						<label class="col-lg-1">:</label>
						<label class="col-lg-8"><?php echo angkaDecimal($selisih_bayar); ?></label>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-12">
			<hr style="margin-top: 10px; margin-bottom: 10px;">
		</div>
		<div class="col-lg-12" style="padding-top: 10px;">
			<button type="button" class="btn btn-success col-lg-12" onclick="ls.printClosingShift(this)" data-id="<?php echo $id_closing_shift; ?>"><i class="fa fa-print"></i> Print Closing Kasir</button>
		</div>
	</div>
</div>