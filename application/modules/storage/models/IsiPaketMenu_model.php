<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class IsiPaketMenu_model extends Conf {
	protected $table = 'isi_paket_menu';

	public function menu()
	{
		return $this->hasOne('\Model\Storage\Menu_model', 'kode_menu', 'menu_kode');
	}
}