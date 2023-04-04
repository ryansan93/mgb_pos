<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class ClosingShift_model extends Conf {
	protected $table = 'closing_shift';
	protected $primaryKey = 'id';

	public function user()
	{
		return $this->hasOne('\Model\Storage\User_model', 'id_user', 'user_id');
	}
}