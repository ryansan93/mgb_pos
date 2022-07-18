<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class JenisPesanan_model extends Conf {
	protected $table = 'jenis_pesanan';
	protected $primaryKey = 'kode';
	protected $kodeTable = 'JPE';
}