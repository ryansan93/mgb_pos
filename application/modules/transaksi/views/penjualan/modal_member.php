<div class="modal-body body no-padding" style="height: 100%;">
	<div class="row">
		<div class="col-lg-12" style="height: 100%;">
			<div class="col-md-12 no-padding" style="padding: 5px;">
				<div class="col-md-12 text-left no-padding">
					<span style="font-weight: bold;">MEMBER</span>
				</div>
			</div>
			<div class="col-md-12 no-padding" style="padding: 5px; height: 87%;">
				<div class="col-md-12 text-center no-padding" style="height: 8%;">
					<table class="table table-bordered" style="margin-bottom: 0px;">
						<thead>
							<tr>
								<th class="col-md-3">Kode</th>
								<th class="col-md-6">Nama</th>
								<th class="col-md-3">No. Telp</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="col-md-12 text-center no-padding list_member" style="height: 92.5%; border-bottom: 1px solid #dddddd;">
					<table class="table table-bordered" style="margin-bottom: 0px;">
						<tbody>
							<?php if ( !empty($data) ): ?>
								<?php foreach ($data as $key => $value): ?>
									<tr class="cursor-p" onclick="jual.pilihMember(this)">
										<td class="col-md-3 text-left kode"><?php echo $value['kode_member']; ?></td>
										<td class="col-md-6 text-left nama"><?php echo $value['nama']; ?></td>
										<td class="col-md-3 text-left no_telp"><?php echo $value['no_telp']; ?></td>
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