<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $key => $value): ?>
		<tr class="cursor-p" onclick="ls.modalListBayar(this)" data-id="<?php echo $value['id']; ?>">
			<td><?php echo $value['user_id']; ?></td>
			<td><?php echo $value['nama']; ?></td>
			<td><?php echo $value['tanggal']; ?></td>
			<td>
				<?php if ( $value['_delete'] == 1 ) : ?>
					<button type="button" class="col-xs-12 btn btn-danger" onclick="ls.delete(this)"><i class="fa fa-trash"></i></button>
				<?php endif ?>
			</td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="3">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>