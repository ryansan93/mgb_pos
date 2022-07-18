<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Branch_model extends Conf {
	protected $table = 'branch';
	protected $primaryKey = 'kode_branch';
	public $timestamps = false;
}