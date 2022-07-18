<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class JenisKartu_model extends Conf {
	protected $table = 'jenis_kartu';
	protected $primaryKey = 'kode_jenis_kartu';
	public $timestamps = false;
}