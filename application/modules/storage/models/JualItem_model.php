<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class JualItem_model extends Conf {
	protected $table = 'jual_item';
	protected $primaryKey = 'kode_faktur_item';
	public $timestamps = false;

	public function getNextKode($kode){
		$id = $this->whereRaw("SUBSTRING(".$this->primaryKey.", 0, ".((strlen($kode)+6)+1).") = '".$kode."'+cast(right(year(current_timestamp),2) as char(2))+replace(str(month(getdate()),2),' ',0)+replace(str(day(getdate()),2),' ',0)")
								->selectRaw("'".$kode."'+right(year(current_timestamp),2)+replace(str(month(getdate()),2),' ',0)+replace(str(day(getdate()),2),' ',0)+replace(str(substring(coalesce(max(".$this->primaryKey."),'00000'),".((strlen($kode)+6)+1).",5)+1,5), ' ', '0') as nextId")
								->first();
		return $id->nextId;
	}

    public function jual_item_detail()
	{
		return $this->hasMany('\Model\Storage\JualItemDetail_model', 'faktur_item_kode', 'kode_faktur_item');
	}

	public function jenis_pesanan()
	{
		return $this->hasMany('\Model\Storage\JenisPesanan_model', 'kode', 'kode_jenis_pesanan');
	}
}