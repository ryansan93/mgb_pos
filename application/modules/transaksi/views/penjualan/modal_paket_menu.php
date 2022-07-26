<div class="modal-body body no-padding" style="height: 100%; font-size: 12px;">
	<div class="row">
		<div class="col-lg-12" style="height: 100%;">
			<div class="col-md-12 no-padding" style="padding: 5px;">
				<div class="col-md-12 text-left no-padding">
					<span style="font-weight: bold;">PAKET MENU</span>
				</div>
			</div>
			<div class="col-md-12 no-padding" style="padding: 5px; height: 85%;">
				<div class="col-md-12 text-center no-padding" style="height: 100%;">
					<table class="table table-bordered" style="margin-bottom: 0px; border: 1px solid #dddddd; height: 100%;">
						<tbody>
							<tr>
								<td class="col-md-2">
									<?php foreach ($data as $k_data => $v_data): ?>
										<div class="col-md-12 no-padding" style="height: 50px; padding-bottom: 5%;">
											<div class="col-md-12 text-center cursor-p button" style="height: 100%; display: flex; justify-content: center; align-items: center;" data-kode="<?php echo $v_data['kode_paket_menu']; ?>">
												<span><b><?php echo $v_data['nama'].' ('.$v_data['max_pilih'].')'; ?></b></span>
											</div>
										</div>
									<?php endforeach ?>
								</td>
								<td class="col-md-10">
									<?php $index = 0; ?>
									<?php foreach ($data as $k_data => $v_data): ?>
										<?php $hide = ($index == 0) ? '' : 'hide'; ?>
										<div class="col-md-12 no-padding detail <?php echo $hide; ?>" data-kode="<?php echo $v_data['kode_paket_menu']; ?>" data-maxpilih="<?php echo $v_data['max_pilih']; ?>">
											<?php foreach ($v_data['isi_paket_menu'] as $k => $val): ?>
												<?php if ( isset($val['menu']['nama']) ): ?>
													<div class="col-md-4 text-left cursor-p no-padding menu_det" style="height: 100%; padding: 0% 1% 0% 1%;">
														<table class="table table-bordered">
															<tbody>
																<tr>
																	<td class="pilih" data-pilih="0" data-kode="<?php echo $val['menu_kode']; ?>">
																		<span><b><?php echo $val['menu']['nama']; ?></b></span>
																		<i class="fa fa-check-circle hide"></i>
																	</td>
																</tr>
																<tr>
																	<td>
																		<div class="col-md-6 no-padding jumlah" style="height: 25px; display: flex; justify-content: center; align-items: center; background-color: #ffffff; border: 1px solid #dedede;" data-min="<?php echo $val['jumlah_min']; ?>" data-max="<?php echo $val['jumlah_max']; ?>">
																			<span style="font-weight: bold;"><?php echo angkaRibuan($val['jumlah_min']); ?></span>
																		</div>
																		<div class="col-md-3 no-padding cursor-p btn-remove disable" style="height: 25px; display: flex; justify-content: center; align-items: center;">
																			<i class="fa fa-minus"></i>
																		</div>
																		<div class="col-md-3 no-padding cursor-p btn-add disable" style="height: 25px; display: flex; justify-content: center; align-items: center;">
																			<i class="fa fa-plus"></i>
																		</div>
																	</td>
																</tr>
															</tbody>
														</table> 
													</div>
												<?php endif ?>
											<?php endforeach ?>
										</div>
										<?php $index++; ?>
									<?php endforeach ?>
								</td>
							</tr>
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