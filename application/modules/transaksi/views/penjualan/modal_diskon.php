<div class="modal-body body no-padding" style="height: 100%;">
	<div class="row">
		<div class="col-lg-12" style="height: 100%;">
			<div class="col-md-12 no-padding" style="padding: 5px;">
				<div class="col-md-12 text-left no-padding">
					<span style="font-weight: bold;">DISKON / PROMO</span>
				</div>
			</div>
			<div class="col-md-12 no-padding" style="padding: 5px; height: 87%;">
				<div class="col-md-12 text-center no-padding" style="height: 8%;">
					<table class="table table-bordered" style="margin-bottom: 0px;">
						<thead>
							<tr>
								<th class="col-md-2">Kode</th>
								<th class="col-md-4">Nama</th>
								<th class="col-md-6">Deskripsi</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="col-md-12 text-center no-padding list_member" style="height: 92.5%; border-bottom: 1px solid #dddddd;">
					<table class="table table-bordered" style="margin-bottom: 0px;">
						<tbody>
							<?php if ( !empty($data) ): ?>
								<?php foreach ($data as $key => $value): ?>
									<tr class="cursor-p header" onclick="jual.pilihDiskon(this)">
										<td class="col-md-2 text-left kode"><?php echo $value['kode']; ?></td>
										<td class="col-md-4 text-left nama"><?php echo $value['nama']; ?></td>
										<td class="col-md-6 text-left"><?php echo $value['deskripsi']; ?></td>
									</tr>
									<tr class="detail" hidden="">
										<td colspan="3">
											<table class="table table-bordered" style="margin-bottom: 0px;">
												<thead>
													<tr>
														<th class="col-md-2">Persen (%)</th>
														<th class="col-md-2">Nilai (Rp)</th>
														<th class="col-md-2">Member</th>
														<th class="col-md-2">Min Beli</th>
														<th class="col-md-2">Level</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach ($value['detail'] as $k_det => $v_det): ?>
														<tr>
															<td class="text-right persen"><?php echo angkaDecimal($v_det['persen']); ?></td>
															<td class="text-right nilai"><?php echo angkaDecimal($v_det['nilai']); ?></td>
															<td class="text-center member" data-nonmember="<?php echo $v_det['non_member']; ?>" data-member="<?php echo $v_det['member']; ?>">
																<?php if ( $v_det['member'] == 1 ) : ?>
																	<i class="fa fa-check"></i>
																<?php else : ?>
																	<i class="fa fa-minus"></i>
																<?php endif ?>
															</td>
															<td class="text-right min_beli"><?php echo angkaDecimal($v_det['min_beli']); ?></td>
															<td class="text-right level"><?php echo $value['level']; ?></td>
														</tr>
													<?php endforeach ?>
												</tbody>
											</table>
										</td>
									</tr>								
								<?php endforeach ?>
							<?php else: ?>
								<tr>
									<td colspan="3">Data tidak ditemukan.</td>
								</tr>
							<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-6 no-padding" style="height: 40px; padding: 5px;">
				<div class="col-md-12 text-center cursor-p btn-cancel button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
					<span><b><i class="fa fa-long-arrow-left"></i></b></span>
				</div>
			</div>
			<div class="col-md-6 no-padding" style="height: 40px; padding: 5px;">
				<div class="col-md-12 text-center cursor-p btn-ok button" style="height: 100%; display: flex; justify-content: center; align-items: center;">
					<span><b><i class="fa fa-long-arrow-right"></i></b></span>
				</div>
			</div>
		</div>
	</div>
</div>