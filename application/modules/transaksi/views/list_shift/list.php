<?php if ( !empty($data) && count($data) > 0 ): ?>
	<?php foreach ($data as $key => $value): ?>
		<tr class="cursor-p" onclick="ls.modalListBayar(this)" data-id="<?php echo $value['id']; ?>">
			<td><?php echo $value['user_id']; ?></td>
			<td><?php echo $value['nama']; ?></td>
			<td><?php echo $value['tanggal']; ?></td>
		</tr>
	<?php endforeach ?>
<?php else: ?>
	<tr>
		<td colspan="3">Data tidak ditemukan.</td>
	</tr>
<?php endif ?>