<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class User_model extends Conf {
	protected $table = 'ms_user';
	protected $primaryKey = 'id_user';
	protected $kodeTable = 'USR';
    public $timestamps = false;

    public function detail_user()
	{
		return $this->hasOne('\Model\Storage\DetUser_model', 'id_user', 'id_user')
					->with(['data_group'])
					->orderBy('id_detuser', 'DESC');
	}
}