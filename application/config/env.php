
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Core Config File
 */

// Site Details
$config['connection'] = array(
	'default' => array(
		'driver'    => 'sqlsrv',

		// NOTE : LIVE DATABASE
		// 'host'      => 'WIN-HA1BBEG29J4\MGB',
		// 'database'  => 'mgb_erp',
		// 'username'  => 'sa',
		// 'password'  => 'Mgb654321',

		// NOTE : LOCAL DATABASE
		'host'      => 'localhost',
		'database'  => 'mgb_pos',
		'username'  => '',
		'password'  => '',

		// NOTE : TEST DATABASE
		// 'host'      => '192.168.100.116',
		// 'database'  => 'ekspedisi_erp',
		// 'username'  => 'it-mt',
		// 'password'  => 'musnimda',

		'charset'   => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix'    => '',
	),
);
