<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class Ppn_model extends Conf {
	protected $table = 'ppn';
	protected $primaryKey = 'id';
	public $timestamps = false;
}