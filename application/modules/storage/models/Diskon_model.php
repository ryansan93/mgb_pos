<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Diskon_model extends Conf{
	protected $table = 'diskon';
	protected $primaryKey = 'kode';

	public function detail()
	{
		return $this->hasMany('\Model\Storage\DiskonDet_model', 'diskon_kode', 'kode');
	}

	public function logs()
	{
		return $this->hasMany('\Model\Storage\LogTables_model', 'tbl_id', 'id')->where('tbl_name', $this->table);
	}
}
