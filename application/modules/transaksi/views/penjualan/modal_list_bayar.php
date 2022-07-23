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
			<div class="panel-heading no-padding">
				<ul class="nav nav-tabs nav-justified">
					<li class="nav-item">
						<a class="nav-link active" data-toggle="tab" href="#belum_bayar" data-tab="belum_bayar">BELUM BAYAR</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-toggle="tab" href="#bayar" data-tab="bayar">BAYAR</a>
					</li>
				</ul>
			</div>
			<div class="panel-body no-padding">
				<div class="tab-content">
					<div id="belum_bayar" class="tab-pane fade show active" role="tabpanel" style="padding-top: 10px;">
						<?php $jml_transaksi = 0; $grand_total = 0; ?>
						<div class="col-lg-12 no-padding">
							<small>
								<table class="table table-bordered tbl_belum_bayar" style="margin-bottom: 0px;">
									<thead>
										<tr>
											<th class="col-lg-3">No. Nota</th>
											<th class="col-lg-4">Pelanggan</th>
											<th class="col-lg-3">Total</th>
										</tr>
									</thead>
									<tbody>
										<?php if ( !empty($data['data_belum_bayar']) ): ?>
											<?php foreach ($data['data_belum_bayar'] as $key => $value): ?>
												<tr class="cursor-p bayar" onclick="jual.openModalPembayaran(this)">
													<td class="kode_faktur"><?php echo $value['kode_faktur']; ?></td>
													<td><?php echo $value['pelanggan']; ?></td>
													<td class="text-right total"><?php echo angkaDecimal($value['total']); ?></td>
												</tr>
												<?php $jml_transaksi++; $grand_total += $value['total']; ?>
											<?php endforeach ?>
										<?php else: ?>
											<tr>
												<td colspan="3">Data tidak ditemukan.</td>
											</tr>
										<?php endif ?>
									</tbody>
								</table>
							</small>
						</div>
						<div class="col-lg-12 no-padding" style="font-size: 10px;">* Klik pada baris untuk melakukan pembayaran</div>
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
						</div>
					</div>
					<div id="bayar" class="tab-pane fade" role="tabpanel" style="padding-top: 10px;">
						<?php $jml_transaksi = 0; $grand_total = 0; ?>
						<div class="col-lg-12 no-padding">
							<small>
								<table class="table table-bordered tbl_bayar" style="margin-bottom: 0px;">
									<thead>
										<tr>
											<th class="col-lg-3">No. Nota</th>
											<th class="col-lg-4">Pelanggan</th>
											<th class="col-lg-3">Total</th>
										</tr>
									</thead>
									<tbody>
										<?php if ( !empty($data['data_bayar']) ): ?>
											<?php foreach ($data['data_bayar'] as $key => $value): ?>
												<tr class="cursor-p bayar" onclick="jual.modalPrint('<?php echo $value['kode_faktur'] ?>')">
													<td><?php echo $value['kode_faktur']; ?></td>
													<td><?php echo $value['pelanggan']; ?></td>
													<td class="text-right"><?php echo angkaDecimal($value['total']); ?></td>
												</tr>
												<?php $jml_transaksi++; $grand_total += $value['total']; ?>
											<?php endforeach ?>
										<?php else: ?>
											<tr>
												<td colspan="3">Data tidak ditemukan.</td>
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
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>