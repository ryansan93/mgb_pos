<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Jual_model extends Conf {
	protected $table = 'jual';
	protected $primaryKey = 'kode_faktur';
	public $timestamps = false;

	public function getNextKode($kode){
		$id = $this->whereRaw("SUBSTRING(".$this->primaryKey.", 0, ".(((strlen($kode)+1)+6)+1).") = '".$kode."'+'-'+cast(right(year(current_timestamp),2) as char(2))+replace(str(month(getdate()),2),' ',0)+replace(str(day(getdate()),2),' ',0)")
								->selectRaw("'".$kode."'+'-'+right(year(current_timestamp),2)+replace(str(month(getdate()),2),' ',0)+replace(str(day(getdate()),2),' ',0)+replace(str(substring(coalesce(max(".$this->primaryKey."),'0000'),".(((strlen($kode)+1)+6)+1).",4)+1,4), ' ', '0') as nextId")
								->first();
		return $id->nextId;
	}

    public function jual_item()
	{
		return $this->hasMany('\Model\Storage\JualItem_model', 'faktur_kode', 'kode_faktur')->with(['jual_item_detail', 'jenis_pesanan']);
	}

	public function jual_diskon()
	{
		return $this->hasMany('\Model\Storage\JualDiskon_model', 'faktur_kode', 'kode_faktur');
	}

	public function bayar()
	{
		return $this->hasMany('\Model\Storage\Bayar_model', 'faktur_kode', 'kode_faktur')->with(['jenis_kartu']);
	}
}