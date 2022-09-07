<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Core Config File
 */

// Site Details
$config['site_version']          = "0.0.73";
$config['public_theme']          = "Default";
$config['admin_theme']           = "Admin";

// Pagination
$config['num_links']             = 8;
$config['full_tag_open']         = "<div class=\"pagination\">";
$config['full_tag_close']        = "</div>";

// Miscellaneous
$config['profiler']              = FALSE;
$config['error_delimeter_left']  = "";
$config['error_delimeter_right'] = "<br />";

/* config untuk ritase forecast panen */
$config['mitrastandart'] = array(
				'bb' => 1.85,
				'dh' => 0.95,
				'umur' => 35

);

$config['bdystandart'] = array(
				'bb' => 1.84,
				'dh' => 0.98,
				'umur' => 32
);

$config['filetimbang'] = "filetimbang/hasiltimbang.log";
$config['btn-collapse'] = '<span class="glyphicon glyphicon-chevron-right cursor-p btn-collapse"></span>';

/* kalau di pusat ganti dengan FM*/
$config['posisi_server'] = 'FM';
$config['server_tujuan'] = 'RPA';

/* untuk upload */
$config['upload_param'] = array(
		'upload_path'          => 'assets/image/real_image/',
		'allowed_types'        => 'jpg|jpeg|png',
		'max_size'             => 10240
);
$config['max_memo_length'] = 100;

$config['status_approve'] = array(
	'approved'  => 'Approved',
	'reviewed'  => 'Reviewed',
	'rejected'  => 'Rejected',
	'submitted' => 'Submitted',
	'nonactive' => 'Non Active',
	'active'    => 'Active'
);

$config['timeline'] = array(
	'master' => array(
			'review'  => array(
					'day' => 1,
					'time'=>'23:59:59'
			),
			'approve' => array(
					'day' => 30,
					'time'=>'23:59:59'
			),
			'edit' => array(
					'day' => 1,
					'time'=>'20:00:00'
			),
	),
	'khusus' => array(
			'review'  => array(
					'day' => 1,
					'time'=>'23:59:59'
			),
			'approve' => array(
					'day' => 30,
					'time'=>'23:59:59'
			),
			'edit' => array(
					'day' => 1,
					'time'=>'15:00:00'
			),
	),
);

$config['SoDo'] = array(
	'profile-rpa'  => array(
		'nama' => 'WONOKOYO JAYA CORPORINDO, PT-RPA',
		'alamat' => 'TAMAN BUNGKUL 1,3,5,7',
		'kode_pembeli' => '0000R999',
		'harga_kontrak' => 'Y',
		'jenis_pengiriman' => 'LOCO'
	),

	'jenis-barang' => array(
		'AB'=> array('kode'=>'AB', 'nama'=>'Ayam Besar'),
	),

);

$config['batas_kontrak_rit'] = 18;

$config['MathOperator'] = array('+','-','/','*','=','<=','>=','<','>');

/*KOLOM TABEL Trans_DetHitungRPAH*/
$config['Trans_DetHitungRPAH'] = array(
	'SelisihEkor_HitungRPAH' => 'Selisih ekor',
	'SelisihKG_HitungRPAH'   => 'Selisih KG',
	'EkorAfkir_HitungRPAH'   => 'Ekor Afkir',
	'KGAfkir_HitungRPAH'     => 'Kg Afkir'
);

/*KOLOM TABEL Trans_RealSJ*/
$config['Trans_RealSJ'] = array(
	'Netto_RealSJ' => 'Kg Netto SJ',
	'BBRata_RealSJ'=> 'BB Rata2 SJ'
);

/*KOLOM TABEL dos_rpharga*/
$config['dos_rpharga'] = array(
	'harga' => 'Bottom Price',
);

/*STATUS SIMPAN*/
$config['g_status'] = array(
	0 => 'delete',
	1 => 'submit',
	2 => 'ack',
	3 => 'approve',
	4 => 'reject',
);

$config['tipe_lokasi'] = array(
	'NG' => 'Negara',
	'PV' => 'Provinsi',
	'KB' => 'Kabupaten',
	'KT' => 'Kota',
	'KC' => 'Kecamatan',
	'DS' => 'Negara',
);

$config['jenis_wilayah'] = array(
	'RG' => 'Regional',
	'PW' => 'Perwakilan',
	'UN' => 'Unit/Area',
);

$config['tipe_kandang'] = array(
	'CH' => 'Closed House',
	'OH' => 'Open House',
	'PG' => 'Panggung',
	'PS' => 'Postal',
);

$config['jenis_mitra'] = array(
	'MR' => 'Mitra Reguler',
	'MB' => 'Mitra Bebas',
);

$config['jabatan'] = array(
	'coo' => 'C.O.O. (Chief Official Officer)',
	'kepala admin' => 'Kepala Admin',
	'admin pusat' => 'Admin Pusat',
	'penanggung jawab' => 'Penanggung Jawab',
	'marketing' => 'Marketing',
	'koordinator' => 'Koordinator',
	'kepala unit' => 'Kepala Unit',
	'penimbang' => 'Penimbang',
	'admin' => 'Admin',
	'ppl' => 'PPL',
	'pembantu' => 'Pembantu',
	'tukang kebun' => 'Tukang Kebun'
);

$config['level_jabatan'] = array(
	'coo' => '0',
	'kepala admin' => '1',
	'admin pusat' => '1',
	'penanggung jawab' => '1',
	'marketing' => '2',
	'koordinator' => '2',
	'kepala unit' => '3',
	'penimbang' => '4',
	'admin' => '4',
	'ppl' => '4',
	'pembantu' => '4',
	'tukang kebun' => '4'
);

$config['atasan'] = array(
	'kepala admin' => array('coo'),
	'penanggung jawab' => array('coo'),
	'admin pusat' => array('coo'),
	'marketing' => array('kepala admin', 'penanggung jawab', 'admin pusat'),
	'koordinator' => array('kepala admin', 'penanggung jawab', 'admin pusat'),
	'kepala unit' => array('koordinator'),
	'penimbang' => array('marketing'),
	'admin' => array('kepala unit'),
	'ppl' => array('kepala unit'),
	'pembantu' => array('kepala unit'),
	'tukang kebun' => array('kepala unit')
);

$config['jenis_ayam'] = array(
	'a' => 'AFKIR',
	'n' => 'NORMAL',
	's' => 'SPESIAL'
);

$config['auth_export_excel'] = array(
  'auth_peternak' => array(
    ['user' => 'admin-mgb', 'pin' => 'AdminMgbPeternak123'],
  ),
  'auth_pelanggan' => array(
    ['user' => 'admin-mgb', 'pin' => 'AdminMgbPelanggan123'],
  ),
);

$config['judul_aplikasi'] = 'COD POS';
$config['nama_aplikasi'] = 'COD FRIED CHICKEN';

$config['paper_size'] = '58';
$config['print_jenis_bayar'] = 1;
