<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Public_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$data = $this->includes;

		$data['title_menu'] = 'dashboard';

		// $content['list_notif'] = $this->list_notif();
		// $content['jml_notif'] = count($this->list_notif());
		$content = null;
		$data['view'] = $this->load->view('home/dashboard', $content, true);

		$this->load->view($this->template, $data);
	}

	public function list_notif()
	{
		$notif = null;
		$arr_fitur = $this->session->userdata()['Fitur']; 
		foreach ($arr_fitur as $key => $v_fitur) {
			foreach ($v_fitur['detail'] as $key => $v_mdetail) {
				$akses = hakAkses('/'.$v_mdetail['path_detfitur']);
				if ( $akses['a_ack'] == 1 ) {
					$status = getStatus('submit');

					$data = Modules::run( $v_mdetail['path_detfitur'].'/model', $status)->first();

					$notif[$v_mdetail['path_detfitur']] = $data->toArray();
					$notif[$v_mdetail['path_detfitur']]['path'] = $v_mdetail['path_detfitur'];
					$notif[$v_mdetail['path_detfitur']]['nama_fitur'] = $v_mdetail['nama_detfitur'];

				} else if ( $akses['a_approve'] == 1 ) {
					$status = getStatus('ack');

					$data = Modules::run( $v_mdetail['path_detfitur'].'/model', $status)->first();

					$notif[$v_mdetail['path_detfitur']] = $data->toArray();
					$notif[$v_mdetail['path_detfitur']]['path'] = $v_mdetail['path_detfitur'];
					$notif[$v_mdetail['path_detfitur']]['nama_fitur'] = $v_mdetail['nama_detfitur'];
				}
			}
        }

        return $notif;
	}

	public function excelToArray(){
		$file = 'order_voadip_mgb.xlsx';
 
		//load the excel library
		$this->load->library('excel');
		 
		//read file from path
		$objPHPExcel = PHPExcel_IOFactory::load($file);
		 
		//get only the Cell Collection
		$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
		$sheet_collection = $objPHPExcel->getSheetNames();

		/* INJEK ORDER VOADIP */
		$_data_header = null;
		foreach ($sheet_collection as $sheet) {
			$sheet_active = $objPHPExcel->setActiveSheetIndexByName($sheet);
			$cell_collection = $sheet_active->getCellCollection();

			foreach ($cell_collection as $cell) {
				$column = $sheet_active->getCell($cell)->getColumn();
				$row = $sheet_active->getCell($cell)->getRow();
				$data_value = $sheet_active->getCell($cell)->getCalculatedValue();

				if ( !empty($data_value) ) {
					if ($row == 1) {
				        $_data_header['header'][$row][$column] = strtoupper($data_value);
				    } else {
				    	if ( isset( $_data_header['header'][1][$column] ) ) {
					    	$_column_val = $_data_header['header'][1][$column];

					    	$val = $data_value;

					    	if ( $_column_val == 'NAMA ITEM' ) {
					    		$m_brg = new \Model\Storage\Barang_model();
								$d_brg = $m_brg->where('nama', trim(strtoupper($val)))->orderBy('id', 'desc')->first();

					    		$_data['value'][$row][$_column_val] = $d_brg->kode;
					    		$_data['value'][$row]['KATEGORI'] = $d_brg->kategori;
					    		$_data['value'][$row]['KEMASAN'] = 'PLASTIK';
					    	} else if ( $_column_val == 'PERUSAHAAN' ) {
					    		$m_perusahaan = new \Model\Storage\Perusahaan_model();
								$d_perusahaan = $m_perusahaan->where('perusahaan', 'like', '%'.trim(strtoupper($val)).'%')->orderBy('version', 'desc')->first();

								if ( empty($d_perusahaan) ) {
									cetak_r( $val );
								} else {
					    			$_data['value'][$row][$_column_val] = $d_perusahaan->kode;
								}
							} else if ( $_column_val == 'SUPPLIER' ) {
					    		$m_supl = new \Model\Storage\Supplier_model();
								$d_supl = $m_supl->where('nama', 'like', '%'.trim(strtoupper($val)).'%')->where('tipe', 'supplier')->orderBy('version', 'desc')->first();

								if ( empty($d_supl) ) {
									cetak_r( $val );
								} else {
					    			$_data['value'][$row][$_column_val] = $d_supl->nomor;
								}
							} else if ( $_column_val == 'GUDANG' ) {
					    		$m_gdg = new \Model\Storage\Gudang_model();
								$d_gdg = $m_gdg->where('nama', 'like', '%'.trim(strtoupper($val)).'%')->where('jenis', 'OBAT')->orderBy('id', 'desc')->first();

				    			$_data['value'][$row][$_column_val] = !empty($d_gdg) ? $d_gdg->id : null;
				    			$_data['value'][$row]['ALAMAT'] = !empty($d_gdg) ? $d_gdg->alamat : null;
				    			$_data['value'][$row]['KIRIM KE'] = 'GUDANG';
				    		} else if ( $_column_val == 'TGL ORDER' || $_column_val == 'TGL KIRIM' ) {
					    		$split = explode('/', $val);
					    		$year = $split[2]; 
					    		$month = (strlen($split[0]) < 2) ? '0'.$split[0] : $split[0];
					    		$day = (strlen($split[1]) < 2) ? '0'.$split[1] : $split[1];
					    		$tgl = $year.'-'.$month.'-'.$day;

					    		$_data['value'][$row][$_column_val] = $tgl;
					    	} else {
					    		$_data['value'][$row][$_column_val] = $val;
					    	}
				    	}
				    }
			    }
			}
		}

		if ( !empty($_data) ) {
			$data = null;
			foreach ($_data['value'] as $k_val => $val) {
				$key = $val['SUPPLIER'].' - '.str_replace('-', '', $val['TGL ORDER']).' - '.$val['GUDANG'];
				$data[ $key ]['SUPPLIER'] = $val['SUPPLIER'];
				$data[ $key ]['TGL ORDER'] = $val['TGL ORDER'];
				$data[ $key ]['GUDANG'] = $val['GUDANG'];
				$data[ $key ]['DETAIL'][] = $val;
			}

			if ( !empty($data) ) {
				foreach ($data as $k_data => $v_data) {
					$m_order_voadip = new \Model\Storage\OrderVoadip_model();
		            $now = $m_order_voadip->getDate();

		            $kode_unit = null;
	                $id_kirim = $v_data['GUDANG'];
	                $jenis_kirim = 'gudang';

		            if ( stristr($jenis_kirim, 'gudang') !== FALSE ) {
		                $m_gdg = new \Model\Storage\Gudang_model();
		                $d_gdg = $m_gdg->where('id', $id_kirim)->with(['dUnit'])->first();

		                if ( $d_gdg ) {
		                    $d_gdg = $d_gdg->toArray();
		                    $kode_unit = $d_gdg['d_unit']['kode'];
		                }
		            }

		            $nomor = $m_order_voadip->getNextNomor('OVO/'.$kode_unit);

		            $id_order = $m_order_voadip->getNextIdentity();

		            $m_order_voadip->id = $id_order;
		            $m_order_voadip->no_order = $nomor;
		            $m_order_voadip->supplier = $v_data['SUPPLIER'];
		            $m_order_voadip->tanggal = $v_data['TGL ORDER'];
		            $m_order_voadip->user_submit = $this->userid;
		            $m_order_voadip->tgl_submit = $now['waktu'];
		            $m_order_voadip->version = 1;
		            $m_order_voadip->save();

		            foreach ($v_data['DETAIL'] as $k_detail => $v_detail) {
		                $m_order_voadip_detail = new \Model\Storage\OrderVoadipDetail_model();

		                $m_order_voadip_detail->id = $m_order_voadip_detail->getNextIdentity();
		                $m_order_voadip_detail->id_order = $m_order_voadip->id;
		                $m_order_voadip_detail->kode_barang = $v_detail['NAMA ITEM'];
		                $m_order_voadip_detail->kemasan = $v_detail['KEMASAN'];
		                $m_order_voadip_detail->harga = $v_detail['HARGA BELI'];
		                $m_order_voadip_detail->harga_jual = isset($v_detail['HARGA JUAL']) ? $v_detail['HARGA JUAL'] : 0;
		                $m_order_voadip_detail->jumlah = $v_detail['JUMLAH'];
		                $m_order_voadip_detail->total = $v_detail['HARGA BELI'] * $v_detail['JUMLAH'];
		                $m_order_voadip_detail->kirim_ke = strtolower($v_detail['KIRIM KE']);
		                $m_order_voadip_detail->alamat = $v_detail['ALAMAT'];
		                $m_order_voadip_detail->kirim = $v_detail['GUDANG'];
		                $m_order_voadip_detail->perusahaan = $v_detail['PERUSAHAAN'];
		                $m_order_voadip_detail->tgl_kirim = $v_detail['TGL KIRIM'];
		                $m_order_voadip_detail->save();
		            }

		            $d_order_voadip = $m_order_voadip->where('id', $id_order)->with(['detail'])->first();

		            $deskripsi_log_order_voadip = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
		            Modules::run( 'base/event/save', $d_order_voadip, $deskripsi_log_order_voadip);
				}
			}
		}


		/* INJEK ORDER DOC */
		// $_data_header = null;
		// foreach ($sheet_collection as $sheet) {
		// 	$sheet_active = $objPHPExcel->setActiveSheetIndexByName($sheet);
		// 	$cell_collection = $sheet_active->getCellCollection();

		// 	foreach ($cell_collection as $cell) {
		// 		$column = $sheet_active->getCell($cell)->getColumn();
		// 		$row = $sheet_active->getCell($cell)->getRow();
		// 		$data_value = $sheet_active->getCell($cell)->getCalculatedValue();

		// 		if ( !empty($data_value) ) {
		// 			if ($row == 1) {
		// 		        $_data_header['header'][$row][$column] = strtoupper($data_value);
		// 		    } else {
		// 		    	if ( isset( $_data_header['header'][1][$column] ) ) {
		// 			    	$_column_val = $_data_header['header'][1][$column];

		// 			    	$val = $data_value;

		// 			    	if ( $_column_val == 'MITRA' ) {
		// 			    		$m_mitra = new \Model\Storage\Mitra_model();
		// 						$d_mitra = $m_mitra->where('nama', trim(strtoupper($data_value)))->orderBy('id', 'desc')->first();

		// 			    		$_data['value'][$row][$_column_val] = ($d_mitra) ? $d_mitra->id : null;
		// 			    	} else if ( $_column_val == 'PERUSAHAAN' ) {
		// 			    		$m_perusahaan = new \Model\Storage\Perusahaan_model();
		// 						$d_perusahaan = $m_perusahaan->where('perusahaan', 'like', '%'.trim(strtoupper($data_value)).'%')->orderBy('version', 'desc')->first();

		// 			    		$_data['value'][$row][$_column_val] = $d_perusahaan->kode;
		// 			    	} else if ( $_column_val == 'SUPPLIER' ) {
		// 			    		$m_supl = new \Model\Storage\Supplier_model();
		// 						$d_supl = $m_supl->where('nama', 'like', '%'.trim(strtoupper($data_value)).'%')->where('tipe', 'supplier')->orderBy('version', 'desc')->first();

		// 			    		$_data['value'][$row][$_column_val] = $d_supl->nomor;
		// 			    	} else if ( $_column_val == 'JENIS DOC' ) {
		// 			    		$m_brg = new \Model\Storage\Barang_model();
		// 						$d_brg = $m_brg->where('nama', 'like', '%'.trim(strtoupper($data_value)).'%')->where('tipe', 'doc')->orderBy('version', 'desc')->first();

		// 			    		$_data['value'][$row][$_column_val] = $d_brg->kode;
		// 			    	} else if ( $_column_val == 'RENCANA TIBA' ) {
		// 			    		$split = explode('/', $data_value);
		// 			    		$year = $split[2]; 
		// 			    		$month = (strlen($split[0]) < 2) ? '0'.$split[0] : $split[0];
		// 			    		$day = (strlen($split[1]) < 2) ? '0'.$split[1] : $split[1];
		// 			    		$tgl = $year.'-'.$month.'-'.$day;

		// 			    		$_data['value'][$row][$_column_val] = $tgl;
		// 			    	} else {
		// 			    		$_data['value'][$row][$_column_val] = $val;
		// 			    	}
		// 		    	}
		// 		    }
		// 	    }
		// 	}
		// }

		// if ( !empty($_data) ) {
		// 	foreach ($_data['value'] as $k_data => $v_data) {
		// 		$noreg = null;

		// 		$m_mitra = new \Model\Storage\Mitra_model();
		// 		$d_mitra = $m_mitra->where('id', $v_data['MITRA'])->with(['dPerwakilans'])->orderBy('id', 'desc')->first();

		// 		$nama_mitra = null;
		// 		$nim = null;
		// 		$id_kdg = null;
		// 		if ( $d_mitra ) {
		// 			$d_mitra = $d_mitra->toArray();
		// 			$nim = $d_mitra['d_perwakilans'][0]['nim'];
		// 			$nama_mitra = $d_mitra['nama'];
					
		// 			$m_mm = new \Model\Storage\MitraMapping_model();
		// 			$d_mm = $m_mm->select('id')->where('nomor', $d_mitra['nomor'])->orderBy('id', 'desc')->get();

		// 			if ( $d_mm->count() > 0 ) {
		// 				$id_mm = $d_mm->toArray();
		// 			}					

		// 			$m_kdg = new \Model\Storage\Kandang_model();
		// 			$d_kdg = $m_kdg->select('id')->whereIn('mitra_mapping', $id_mm)->where('kandang', $v_data['KANDANG'])->orderBy('id', 'desc')->get();

		// 			if ( $d_kdg->count() > 0 ) {
		// 				$id_kdg = $d_kdg->toArray();
		// 			}
		// 		}

		// 		if ( !empty($nim) && count($id_kdg) > 0 ) {
		// 			$m_rs = new \Model\Storage\RdimSubmit_model();
		// 			$d_rs = $m_rs->where('nim', $nim)->whereIn('kandang', $id_kdg)->where('tgl_docin', '<=', $v_data['RENCANA TIBA'])->first();

		// 			if ( $d_rs ) {
		// 				$noreg = $d_rs->noreg;
		// 			}
		// 		}

		// 		if ( empty($noreg) ) {
		// 			cetak_r( $nama_mitra.' | '.$v_data['KANDANG'] );
		// 		} else {
		// 			$m_order_doc = new \Model\Storage\OrderDoc_model();
		//             $now = $m_order_doc->getDate();

		//             $m_rs = new \Model\Storage\RdimSubmit_model();
		//             $d_rs = $m_rs->where('noreg', $noreg)->with(['dKandang'])->first();

		//             $kode_unit = null;
		//             if ( $d_rs ) {
		//                 $d_rs = $d_rs->toArray();
		//                 $kode_unit = $d_rs['d_kandang']['d_unit']['kode'];
		//             }

		//             $nomor = $m_order_doc->getNextNomor('ODC/'.$kode_unit);

		//             $m_order_doc->id = $m_order_doc->getNextIdentity();
		//             $m_order_doc->no_order = $nomor;
		//             $m_order_doc->noreg = $noreg;
		//             $m_order_doc->supplier = $v_data['SUPPLIER'];
		//             $m_order_doc->item = $v_data['JENIS DOC'];
		//             $m_order_doc->jml_ekor = $v_data['EKOR'];
		//             $m_order_doc->jml_box = ($v_data['EKOR'] / 100);
		//             $m_order_doc->rencana_tiba = $v_data['RENCANA TIBA'];
		//             $m_order_doc->user_submit = $this->userid;
		//             $m_order_doc->tgl_submit = prev_date($d_rs['tgl_docin']).' '.substr($now['waktu'], 11, 5);
		//             $m_order_doc->keterangan = '-';
		//             $m_order_doc->version = 1;
		//             $m_order_doc->perusahaan = $v_data['PERUSAHAAN'];
		//             $m_order_doc->jns_box = $v_data['JENIS BOX'];
		//             $m_order_doc->harga = $v_data['HARGA'];
		//             $m_order_doc->total = ($v_data['EKOR'] * $v_data['HARGA']);
		//             $m_order_doc->save();

		//             $deskripsi_log_order_doc = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
		//             Modules::run( 'base/event/save', $m_order_doc, $deskripsi_log_order_doc);
		// 		}
		// 	}
		// }
		/* END - INJEK ORDER DOC */

		/* INJEK RDIM */
		/* $_data = null;
		$_data_header = null;
		foreach ($sheet_collection as $sheet) {
			$sheet_active = $objPHPExcel->setActiveSheetIndexByName($sheet);
			$cell_collection = $sheet_active->getCellCollection();

			foreach ($cell_collection as $cell) {
				$column = $sheet_active->getCell($cell)->getColumn();
				$row = $sheet_active->getCell($cell)->getRow();
				$data_value = $sheet_active->getCell($cell)->getCalculatedValue();

				if ( !empty($data_value) ) {
					if ($row == 1) {
				        $_data_header['header'][$row][$column] = strtoupper($data_value);
				    } else {
				    	if ( isset( $_data_header['header'][1][$column] ) ) {
					    	$_column_val = $_data_header['header'][1][$column];

					    	$val = $data_value;

					    	if ( $_column_val == 'MITRA' ) {
					    		$m_mitra = new \Model\Storage\Mitra_model();
								$d_mitra = $m_mitra->where('nama', trim(strtoupper($data_value)))->orderBy('id', 'desc')->first();

					    		$_data['value'][$row][$_column_val] = ($d_mitra) ? $d_mitra->id : null;
					    	} else if ( $_column_val == 'TGL DOCIN' || $_column_val == 'KONTRAK' ) {
					    		$split = explode('/', $data_value);
					    		$year = $split[2]; 
					    		$month = (strlen($split[0]) < 2) ? '0'.$split[0] : $split[0];
					    		$day = (strlen($split[1]) < 2) ? '0'.$split[1] : $split[1];
					    		$tgl = $year.'-'.$month.'-'.$day;

					    		$_data['value'][$row][$_column_val] = $tgl;


					    		if ( $_column_val == 'KONTRAK' ) {
					    			$m_pm = new \Model\Storage\PerwakilanMaping_model();
									$d_pm = $m_pm->where('id_pwk', $_data['value'][$row]['PERWAKILAN'])->get();

									if ( $d_pm->count() > 0 ) {
										$d_pm = $d_pm->toArray();

										foreach ($d_pm as $k_pm => $v_pm) {
											$m_hbi = new \Model\Storage\HitungBudidayaItem_model();
											$d_hbi = $m_hbi->where('id', $v_pm['id_hbi'])->first();

											if ( $d_hbi ) {
												$m_sk = new \Model\Storage\SapronakKesepakatan_model();
												$d_sk = $m_sk->where('id', $d_hbi->id_sk)->where('mulai', '<=', $tgl)->orderBy('version', 'desc')->first();

												if ( $d_sk ) {
													$m_pk = new \Model\Storage\PolaKerjasama_model();
													$d_pk = $m_pk->where('id', $d_sk['pola'])->first();

													$pola_kerjasama = $d_pk->item_code.' ('.trim($d_sk['item_pola']).')';

													$_data['value'][$row]['FORMAT PB'] = $v_pm['id'];
													$_data['value'][$row]['POLA MITRA'] = $pola_kerjasama;
													$_data['value'][$row]['PERUSAHAAN'] = $d_sk['perusahaan'];
												}
											}
										}
									}
									
						    		$_data['value'][$row][$_column_val] = $tgl;
						    	}

					    	} else if ( $_column_val == 'PERWAKILAN' ) {
					    		$nama_perwakilan = null;
					    		if ( stristr($data_value, 'jatim') !== FALSE ) {
					    			$nama_perwakilan = 'Jawa Timur '.substr(trim($data_value), -2);
					    		}
					    		if ( stristr($data_value, 'jateng') !== FALSE ) {
					    			$nama_perwakilan = 'Jawa Tengah '.substr(trim($data_value), -2);
					    		}

					    		$m_wilayah = new \Model\Storage\Wilayah_model();
								$d_wilayah = $m_wilayah->where('nama', 'like', '%'.$nama_perwakilan.'%')->where('jenis', 'PW')->first();

					    		$_data['value'][$row][$_column_val] = $d_wilayah->id;
					    	} else if ( $_column_val == 'VAKSIN' ) {
					    		$m_vaksin = new \Model\Storage\Vaksin_model();
								$d_vaksin = $m_vaksin->where('nama_vaksin', 'like', '%'.trim($data_value).'%')->first();

					    		$_data['value'][$row][$_column_val] = $d_vaksin->id;
					    	} else if ( $_column_val == 'PENGAWAS' || $_column_val == 'TIM SAMPLING' || $_column_val == 'TIM PANEN' || $_column_val == 'KOAR' ) {
					    		$m_karyawan = new \Model\Storage\Karyawan_model();
								$d_karyawan = $m_karyawan->where('nama', 'like', '%'.trim(strtolower($data_value)).'%')->first();

								if ( !$d_karyawan ) {
									cetak_r( $data_value );
								}

					    		$_data['value'][$row][$_column_val] = $d_karyawan->nik;
					    	} else if ( $_column_val == 'KANDANG' ) {
					    		$m_mm = new \Model\Storage\MitraMapping_model();
								$d_mm = $m_mm->select('id')->where('mitra', $_data['value'][$row]['MITRA'])->get();

								if ( $d_mm->count() > 0 ) {
									$d_mm = $d_mm->toArray();

									$m_kdg = new \Model\Storage\Kandang_model();
									$d_kdg = $m_kdg->whereIn('mitra_mapping', $d_mm)->where('kandang', $data_value)->first();

									if ( !$d_kdg ) {
										cetak_r($d_mm);
										cetak_r($_data['value'][$row]['MITRA'].' | '.$data_value);
									}

					    			$_data['value'][$row][$_column_val] = $d_kdg->id;
					    			$_data['value'][$row]['GROUP'] = 'G-'.$d_kdg->grup;
								}
					    	} else {
					    		$_data['value'][$row][$_column_val] = $data_value;
					    	}
				    	}
				    }
			    }
			}
		}

		if ( !empty($_data) ) {
			foreach ($_data['value'] as $k_data => $v_data) {
				$noreg = null;

		        $m_mmp = new \Model\Storage\MitraMapping_model();
		        $d_mmp = $m_mmp->where('mitra', $v_data['MITRA'])->orderBy('id', 'desc')->first();

		        $m_kandang = new \Model\Storage\Kandang_model();
		        $d_kandang = $m_kandang->where('id', $v_data['KANDANG'])
		                               ->first();

		        $_nim = trim($d_mmp->nim);

		        $m_rdims = new \Model\Storage\RdimSubmit_model();
		        $d_rdims = $m_rdims->where('nim', $d_mmp->nim)
		                           ->where('kandang', $d_kandang->id)
		                           ->orderBy('tgl_docin', 'DESC')
		                           ->first();

		        if ( empty($d_rdims) ) {
		        	$str_kandang = null;
		            if ( strlen(number_format($d_kandang->kandang)) < 2) {
		                $str_kandang = '0'.number_format($d_kandang->kandang);
		            } else {
		                $str_kandang = $d_kandang->kandang;
		            }

		            $noreg = trim($_nim) . '01' . $str_kandang;
		        } else {
		            $_noreg = $d_rdims->noreg;
		            $jml_nim = strlen(trim($d_mmp->nim));

		            $_siklus = trim(substr($_noreg, $jml_nim, 2));
		            $siklus = $_siklus + 1;

		            $str_siklus = null;
		            if ( strlen($siklus) == 1) {
		                $str_siklus = '0'.$siklus;
		            } else {
		                $str_siklus = $siklus;
		            }

		            $str_kandang = null;
		            if ( strlen(number_format($d_kandang->kandang)) < 2) {
		                $str_kandang = '0'.number_format($d_kandang->kandang);
		            } else {
		                $str_kandang = $d_kandang->kandang;
		            }

		            $noreg = $_nim . $str_siklus . $str_kandang;
		        }

		        $strtotime = strtotime($v_data['TGL DOCIN']);
				$weekStartDate = date('Y-m-d',strtotime("last Sunday", $strtotime));
	            $weekEndDate = date('Y-m-d',strtotime("first Saturday", $strtotime));

	            $week = array(
	                'start_date' => $weekStartDate,
	                'end_date' => $weekEndDate,
	            );

		        // NOTE: 1. save header -> rdim
		        $m_rdim = new \Model\Storage\Rdim_model();
		        $d_rdim = $m_rdim->where('mulai', $weekStartDate)->where('selesai', $weekEndDate)->first();

		        if ( !$d_rdim ) {
		        	$m_rdim = new \Model\Storage\Rdim_model();
			        $next_doc_number = $m_rdim->getNextDocNum('ADM/RDIM');

			        $m_rdim->nomor = $next_doc_number;
			        $m_rdim->mulai = $weekStartDate;
			        $m_rdim->selesai = $weekEndDate;
			        $m_rdim->g_status = getStatus('submit');
			        $m_rdim->save();

			        $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
			        Modules::run( 'base/event/save', $m_rdim, $deskripsi_log);
			        $id_rdim = $m_rdim->id;
		        } else {
		        	$id_rdim = $d_rdim->id;
		        }

	            $m_rs = new \Model\Storage\RdimSubmit_model();
	            $m_rs->id_rdim = $id_rdim;
	            $m_rs->tgl_docin = $v_data['TGL DOCIN'] ;
	            $m_rs->nim = $_nim ;
	            $m_rs->kandang = $v_data['KANDANG'] ;
	            $m_rs->populasi = $v_data['POPULASI'] ;
	            $m_rs->noreg = $noreg ;
	            $m_rs->pengawas = $v_data['PENGAWAS'] ;
	            $m_rs->sampling = $v_data['TIM SAMPLING'] ;
	            $m_rs->tim_panen = $v_data['TIM PANEN'] ;
	            $m_rs->koar = $v_data['KOAR'] ;
	            $m_rs->format_pb = $v_data['FORMAT PB'] ;
	            $m_rs->pola_mitra = $v_data['POLA MITRA'] ;
	            $m_rs->grup = $v_data['GROUP'];
	            $m_rs->status = 1;
	            $m_rs->tipe_densitas = null;
	            $m_rs->perusahaan = $v_data['PERUSAHAAN'];
	            $m_rs->vaksin = $v_data['VAKSIN'];
	            $m_rs->save();

	            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
	            Modules::run( 'base/event/save', $m_rs, $deskripsi_log);
			}
		}
		*/

		/* INJEK OVK */
		/* 
		$_data = null;
		$_data_header = null;
		foreach ($sheet_collection as $sheet) {
			$sheet_active = $objPHPExcel->setActiveSheetIndexByName($sheet);
			$cell_collection = $sheet_active->getCellCollection();

			foreach ($cell_collection as $cell) {
				$column = $sheet_active->getCell($cell)->getColumn();
				$row = $sheet_active->getCell($cell)->getRow();
				$data_value = $sheet_active->getCell($cell)->getCalculatedValue();

				if ( !empty($data_value) ) {
					if ($row == 1) {
				        $_data_header['header'][$row][$column] = strtoupper($data_value);
				    } else {
				    	if ( isset( $_data_header['header'][1][$column] ) ) {
					    	$_column_val = $_data_header['header'][1][$column];

					    	$val = $data_value;

					    	if ( $_column_val == 'SUPPLIER' ) {
					    		$kode_supl = null;
					    		$m_supplier = new \Model\Storage\Supplier_model();
								$d_supplier = $m_supplier->where('nama', 'like', '%'.trim(strtolower($data_value)).'%')->first();

					    		$_data['value'][$row][$_column_val] = ($d_supplier) ? $d_supplier->nomor : null;
					    	} else {
					    		$_data['value'][$row][$_column_val] = ($_column_val == 'KATEGORI') ? strtolower($val) : $val;
					    	}
				    	}
				    }
			    }
			}
		}

		if ( !empty($_data) ) {
			foreach ($_data['value'] as $k_data => $v_data) {
				$m_brg = new \Model\Storage\Barang_model();
                $nomor = $m_brg->getNextIdVoadip();

                $m_brg->kode = $v_data['KODE'];
                $m_brg->kode_supplier = $v_data['SUPPLIER'];
                $m_brg->kategori = $v_data['KATEGORI'];
                $m_brg->nama = $v_data['NAMA'];
                $m_brg->berat = $v_data['BERAT'];
                $m_brg->bentuk = $v_data['BENTUK'];
                $m_brg->simpan = $v_data['SIMPAN'];
                $m_brg->isi = $v_data['ISI'];
                $m_brg->satuan = $v_data['SATUAN'];
                $m_brg->g_status = $v_data['G_STATUS'];
                $m_brg->tipe = 'obat';
                $m_brg->version = 1;
                $m_brg->save();

                $deskripsi_log_voadip = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run( 'base/event/save', $m_brg, $deskripsi_log_voadip );
			}
		}
		*/

		// $_data = null;
		// $_data_kecamatan = null;
		// foreach ($sheet_collection as $sheet) {
		// 	$sheet_active = $objPHPExcel->setActiveSheetIndexByName($sheet);
		// 	$cell_collection = $sheet_active->getCellCollection();
		// 	$bangunan_kandang = null;
		// 	$key = null;
		// 	foreach ($cell_collection as $cell) {
		// 		$column = $sheet_active->getCell($cell)->getColumn();
		// 		$row = $sheet_active->getCell($cell)->getRow();
		// 		$data_value = $sheet_active->getCell($cell)->getCalculatedValue();

		// 		$column_idx = PHPExcel_Cell::columnIndexFromString($column);

		// 		if ($row == 1) {
		// 			if ( !empty($data_value) ) {
		// 	        	$_data[strtolower($sheet)]['header'][$row][$column] = strtoupper($data_value);
		// 			}
		// 	    } else {
		// 	    	if ( strtolower($sheet) == 'mitra' ) {
		// 	    		if ( !empty($data_value) ) {
		// 		    		$val = strtoupper($data_value);
		// 		    		if ( $column == 'G' || 
		// 			    		 $column == 'H' || 
		// 			    		 $column == 'I' ) {
		// 				    	$m_lokasi = new \Model\Storage\Lokasi_model();
		// 				    	$d_lokasi = $m_lokasi->where('nama', 'like', '%'.trim($data_value).'%')->first();

		// 				    	if ( !empty($d_lokasi) ) {
		// 				    		$val = $d_lokasi->id;
		// 				    	} else {
		// 				    		$_data_kecamatan[ $data_value ] = $data_value;
		// 				    	}
		// 			    	}
		// 	        		$_data[strtolower($sheet)]['value'][$row][$column] = $val;
		// 	        	}
		// 	    	}  else {
		// 	    		if ( !empty($data_value) ) {
		// 		    		if ( $column_idx <= 16 ) {
		// 		    			$val = strtoupper($data_value);
		// 			    		if ( $column == 'H' || 
		// 				    		 $column == 'I' || 
		// 				    		 $column == 'J' || 
		// 				    		 $column == 'K' ) {
		// 			    			if ( $column == 'H' ) {
		// 			    				$m_wilayah = new \Model\Storage\Wilayah_model();
		// 						    	$d_wilayah = $m_wilayah->where('nama', 'like', '%'.trim($data_value).'%')->first();

		// 						    	if ( !empty($d_wilayah) ) {
		// 						    		$val = $d_wilayah->id;
		// 						    	}
		// 			    			} else {
		// 						    	$m_lokasi = new \Model\Storage\Lokasi_model();
		// 						    	$d_lokasi = $m_lokasi->where('nama', 'like', '%'.trim($data_value).'%')->first();

		// 						    	if ( !empty($d_lokasi) ) {
		// 						    		$val = $d_lokasi->id;
		// 						    	} else {
		// 						    		$_data_kecamatan[ $data_value ] = $data_value;
		// 						    	}
		// 			    			}
		// 				    	}
		// 		    			$_data[strtolower($sheet)]['value'][$row][$column] = $val;
		// 		    		} else {
		// 		    			if ( !empty($data_value) ) {
		// 		    				$key = substr($_data[strtolower($sheet)]['header'][1][$column], -8);
		// 	    					$_data[strtolower($sheet)]['value'][$row]['bangunans'][$key][$column] = strtoupper($data_value);
		// 		    			}
		// 		    		}
		// 	    		}
		// 	    	}
		// 	    }
		// 	}
		// }

		// $data = null;
		// if ( count($_data['mitra']['value']) > 0 ) {
		// 	foreach ($_data['mitra']['value'] as $k_data => $v_data) {
		// 		$data[$k_data] = $v_data;

		// 		$m_wilayah = new \Model\Storage\Wilayah_model();
		//     	$d_wilayah = $m_wilayah->where('nama', 'like', '%'.trim($_data['kdg1']['value'][$k_data]['A']).'%')->first();
		//     	$perwakilan = null;
		//     	if ( !empty($d_wilayah) ) {
		//     		$perwakilan = $d_wilayah->id;
		//     	}

		// 		$data[$k_data]['perwakilan'] = $perwakilan;
		// 		if ( isset($_data['kdg1']['value'][$k_data]) ) {
		// 			$data[$k_data]['kandangs']['kdg1'] = $_data['kdg1']['value'][$k_data];
		// 		}
		// 		if ( isset($_data['kdg2']['value'][$k_data]) ) {
		// 			$data[$k_data]['kandangs']['kdg2'] = $_data['kdg2']['value'][$k_data];
		// 		}
		// 		if ( isset($_data['kdg3']['value'][$k_data]) ) {
		// 			$data[$k_data]['kandangs']['kdg3'] = $_data['kdg3']['value'][$k_data];
		// 		}
		// 		if ( isset($_data['kdg4']['value'][$k_data]) ) {
		// 			$data[$k_data]['kandangs']['kdg4'] = $_data['kdg4']['value'][$k_data];
		// 		}
		// 		if ( isset($_data['kdg5']['value'][$k_data]) ) {
		// 			$data[$k_data]['kandangs']['kdg5'] = $_data['kdg5']['value'][$k_data];
		// 		}
		// 		if ( isset($_data['kdg6']['value'][$k_data]) ) {
		// 			$data[$k_data]['kandangs']['kdg6'] = $_data['kdg6']['value'][$k_data];
		// 		}
		// 		if ( isset($_data['kdg7']['value'][$k_data]) ) {
		// 			$data[$k_data]['kandangs']['kdg7'] = $_data['kdg7']['value'][$k_data];
		// 		}
		// 	}
		// }

		// // cetak_r( $data, 1 );
		// // cetak_r( $_data_kecamatan, 1 );

		// // $m_mitra = new \Model\Storage\Mitra_model();
		// // $d_mitra = $m_mitra->where('id', 102)->with(['telepons', 'perwakilans'])->first();

  //       // $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
  //       // Modules::run( 'base/event/save', $d_mitra, $deskripsi_log );

  //       // cetak_r( $data, 1 );

		// $m_mitra = new \Model\Storage\Mitra_model();
		// $d_mitra = $m_mitra->delete();

		// foreach ($data as $k => $val) {
		// 	$status = 'submit';
  //           // NOTE: 1. simpan mitra
  //           $m_mitra = new \Model\Storage\Mitra_model();
  //           $id_mitra = $m_mitra->getNextIdentity();
  //           $nomor_mitra = $m_mitra->getNextNomor();

  //           $m_mitra->id = $id_mitra;
  //           $m_mitra->nomor = $nomor_mitra;
  //           $m_mitra->nama = $val['A'];
  //           $m_mitra->ktp = !empty($val['C']) ? str_replace('.', '', $val['C']) : '';
  //           $m_mitra->npwp = !empty($val['D']) ? str_replace('-', '', str_replace('.', '', $val['D'])) : null;
  //           $m_mitra->alamat_kecamatan = $val['I'];
  //           $m_mitra->alamat_kelurahan = $val['J'];
  //           $m_mitra->alamat_rt = $val['L'];
  //           $m_mitra->alamat_rw = $val['M'];
  //           $m_mitra->alamat_jalan = $val['K'];
  //           $m_mitra->bank = !empty($val['N']) ? $val['N'] : '-';
  //           $m_mitra->rekening_cabang_bank = !empty($val['O']) ? $val['O'] : '-';
  //           $m_mitra->rekening_nomor = !empty($val['P']) ? $val['P'] : '-';
  //           $m_mitra->rekening_pemilik = !empty($val['Q']) ? $val['Q'] : '-';
  //           $m_mitra->status = $status;
  //           $m_mitra->keterangan_jaminan = $val['R'];
  //           $m_mitra->jenis = 'MR';
  //           $m_mitra->mstatus = 1;
  //           $m_mitra->version = 1;
  //           $m_mitra->save();

  //           $m_telp = new \Model\Storage\TeleponMitra_model();
  //           $m_telp->id = $m_telp->getNextIdentity();
  //           $m_telp->mitra = $id_mitra;
  //           $m_telp->nomor = $val['F'];
  //           $m_telp->save();

  //           $m_mitra_mapping = new \Model\Storage\MitraMapping_model();
  //           $mitra_mapping_id = $m_mitra_mapping->getNextIdentity();
  //           $m_mitra_mapping->id = $mitra_mapping_id;
  //           $m_mitra_mapping->mitra = $id_mitra;
  //           $m_mitra_mapping->perwakilan = $val['perwakilan'];
  //           $m_mitra_mapping->nim = $val['B'];
  //           $m_mitra_mapping->nomor = $nomor_mitra;
  //           $m_mitra_mapping->save();

  //           // NOTE: 2.1 simpan kandang
  //           foreach ($val['kandangs'] as $kandang) {
  //           	$tipe_kdg = null;
  //           	$_tipe_kandang = $this->config->item('tipe_kandang');
  //           	foreach ($_tipe_kandang as $k_tk => $v_tk) {
  //           		// cetak_r( trim(strtolower($kandang['F'])).'=='.trim(strtolower($v_tk)) );
  //           		if ( trim(strtolower($kandang['F'])) == trim(strtolower($v_tk)) ) {
  //           			// cetak_r( 'true' );
  //           			$tipe_kdg = $k_tk;
  //           		} else{
  //           			// cetak_r( 'false' );
  //           		}
  //           	}

  //               $m_kandang = new \Model\Storage\Kandang_model();
  //               $kandang_id = $m_kandang->getNextIdentity();
  //               $m_kandang->id = $kandang_id;
  //               $m_kandang->mitra_mapping = $mitra_mapping_id;
  //               $m_kandang->kandang = $kandang['D'];
  //               $m_kandang->unit = $kandang['H'];
  //               $m_kandang->tipe = $tipe_kdg;
  //               $m_kandang->ekor_kapasitas = $kandang['E'];
  //               $m_kandang->alamat_kecamatan = $kandang['K'];
  //               $m_kandang->alamat_kelurahan = $kandang['L'];
  //               $m_kandang->alamat_rt = !empty($kandang['N']) ? $kandang['N'] : 0;
  //               $m_kandang->alamat_rw = !empty($kandang['O']) ? $kandang['O'] : 0;
  //               $m_kandang->alamat_jalan = $kandang['M'];
  //               $m_kandang->ongkos_angkut = $kandang['P'];
  //               $m_kandang->grup = $kandang['C'];
  //               $m_kandang->status = 1;
  //               $m_kandang->save();

  //               if ( !empty($kandang['bangunans']) ) {
	 //                foreach ($kandang['bangunans'] as $bangunan) {
	 //                    $m_bangunan_kandang = new \Model\Storage\BangunanKandang_model();
	 //                    $m_bangunan_kandang->id = $m_bangunan_kandang->getNextIdentity();
	 //                    $m_bangunan_kandang->kandang = $kandang_id;
	 //                    $bangunan_kdg = 0; $meter_panjang = 0; $meter_lebar = 0; $jml_unit = 0;

	 //                    $idx = 0;
	 //                    foreach ($bangunan as $k_bgn => $v_bgn) {
	 //                    	if ( $idx == 0 ) { $bangunan_kdg = $v_bgn; }
	 //                    	if ( $idx == 1 ) { $meter_panjang = $v_bgn; }
	 //                    	if ( $idx == 2 ) { $meter_lebar = $v_bgn; }
	 //                    	if ( $idx == 3 ) { $jml_unit = $v_bgn; }
	 //                    	$idx++;
	 //                    }
	 //                    $m_bangunan_kandang->bangunan = $bangunan_kdg;
	 //                    $m_bangunan_kandang->meter_panjang = $meter_panjang;
	 //                    $m_bangunan_kandang->meter_lebar = $meter_lebar;
	 //                    $m_bangunan_kandang->jumlah_unit = $jml_unit;
	 //                    $m_bangunan_kandang->save();
	 //                }
  //               }
  //           }

  //           $d_mitra = $m_mitra->where('id', $id_mitra)->with(['telepons', 'perwakilans'])->first();

  //           $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
  //           Modules::run( 'base/event/save', $d_mitra, $deskripsi_log );
		// }
		 
		// echo "Berhasil";

		// // //extract to a PHP readable array format
		// // $idx_header = 0;
		// // $idx_val = 0;
		// // $row_old = null;
		// // foreach ($cell_collection as $cell) {
		// //     $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
		// //     $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
		// //     $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
		 
		// //     //The header will/should be in row 1 only. of course, this can be modified to suit your need.
		// //     if ($row == 1) {
		// //         $header[$row][$column] = strtoupper($data_value);
		// //     } else {
		// //     	$val = trim($data_value);
		// //     	// if ( $column == 'G' || 
		// //     	// 	 $column == 'H' || 
		// //     	// 	 $column == 'I' || 
		// //     	// 	 $column == 'O' || 
		// //     	// 	 $column == 'P' || 
		// //     	// 	 $column == 'Q' ) {
		// // 	    // 	$m_lokasi = new \Model\Storage\Lokasi_model();
		// // 	    // 	$d_lokasi = $m_lokasi->where('nama', 'like', '%'.trim($data_value).'%')->first();

		// // 	    // 	if ( !empty($d_lokasi) ) {
		// // 	    // 		$val = $d_lokasi->id;
		// // 	    // 	}
		// //     	// }
		// //         $arr_data[$row][$column] = strtoupper($val);
		// //     }
		// // }
		 
		// // // send the data in an array format
		// // $data['header'] = $header;
		// // $data['values'] = $arr_data;

		// // $m_eks = new \Model\Storage\Supplier_model();
		// // $d_id_ekspedisi = $m_eks->select('id')->where('tipe', 'supplier')->where('jenis', 'ekspedisi')->get();
		// // if ( $d_id_ekspedisi->count() > 0 ) {
		// // 	$d_id_ekspedisi = $d_id_ekspedisi->toArray();

		// // 	$m_tplg = new \Model\Storage\TelpPelanggan_model();
		// // 	$m_tplg->whereIn('pelanggan', $d_id_ekspedisi)->delete();

		// // 	$m_bplg = new \Model\Storage\BankPelanggan_model();
		// // 	$m_bplg->whereIn('pelanggan', $d_id_ekspedisi)->delete();

		// // 	$m_eks = new \Model\Storage\Supplier_model();
		// // 	$m_eks->whereIn('id', $d_id_ekspedisi)->delete();
		// // }

		// // foreach ($data['values'] as $k_val => $v_val) {
		// // 	$m_eks = new \Model\Storage\Supplier_model();
		// // 	$d_eks = $m_eks->where('nama', 'like', '%'.$v_val['B'].'%')->first();

		// // 	if ( !$d_eks ) {
		// // 		// pelanggan
		// // 		$m_ekspedisi = new \Model\Storage\Supplier_model();
		// // 		$pelanggan_id = $m_ekspedisi->getNextIdentity();

		// // 		$m_ekspedisi->id = $pelanggan_id;		
		// // 		$m_ekspedisi->jenis = 'ekspedisi';
		// // 		$kode_jenis = "B";
		// // 		$m_ekspedisi->nomor = $m_ekspedisi->getNextNomor($kode_jenis);
		// // 		$m_ekspedisi->nama = $v_val['B'];
		// // 		$m_ekspedisi->nik = $v_val['F'];
		// // 		$m_ekspedisi->cp = $v_val['C'];
		// // 		$m_ekspedisi->npwp = $v_val['N'];
		// // 		$m_ekspedisi->alamat_kecamatan = $v_val['I'];
		// // 		$m_ekspedisi->alamat_kelurahan = $v_val['J'];
		// // 		$m_ekspedisi->alamat_rt = $v_val['L'];
		// // 		$m_ekspedisi->alamat_rw = $v_val['M'];
		// // 		$m_ekspedisi->alamat_jalan = $v_val['K'];
		// // 		$m_ekspedisi->usaha_kecamatan = $v_val['Q'];
		// // 		$m_ekspedisi->usaha_kelurahan = $v_val['R'];
		// // 		$m_ekspedisi->usaha_rt = $v_val['T'];
		// // 		$m_ekspedisi->usaha_rw = $v_val['U'];
		// // 		$m_ekspedisi->usaha_jalan = $v_val['S'];
		// // 		$m_ekspedisi->status = 'submit';
		// // 		$m_ekspedisi->mstatus = 1;
		// // 		$m_ekspedisi->tipe = 'supplier';
		// // 		$m_ekspedisi->version = 1;
		// // 		$m_ekspedisi->save();

		// // 		$m_telp = new \Model\Storage\TelpPelanggan_model();
		// // 		$m_telp->id = $m_telp->getNextIdentity();
		// // 		$m_telp->pelanggan = $pelanggan_id;
		// // 		$m_telp->nomor = $v_val['E'];
		// // 		$m_telp->save();

		// // 		$m_bank = new \Model\Storage\BankPelanggan_model();
	 // //    		$bank_eks_id = $m_bank->getNextIdentity();

	 // //    		$m_bank->id = $bank_eks_id;
	 // //    		$m_bank->pelanggan = $pelanggan_id;
	 // //    		$m_bank->bank = $v_val['X'];
	 // //    		$m_bank->rekening_nomor = $v_val['V'];
	 // //    		$m_bank->rekening_pemilik = $v_val['W'];
	 // //    		$m_bank->rekening_cabang_bank = $v_val['Y'];
	 // //    		$m_bank->save();

	 // //    		$d_ekspedisi = $m_ekspedisi->where('id', $pelanggan_id)->with(['telepons', 'banks'])->first();

	 // //    		$deskripsi_log_ekspedisi = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
		// // 		Modules::run( 'base/event/save', $d_ekspedisi, $deskripsi_log_ekspedisi );
		// // 	}
		// // }

		// // return $data;
		
		// // echo "<pre>";
		// // print_r($data);
		// // echo "</pre>";
	}

	public function insert_terima_pakan()
	{
		$m_kirim_pakan = new \Model\Storage\KirimPakan_model();
		$d_kirim_pakan = $m_kirim_pakan->whereBetween('tgl_kirim', ['2021-12-20', '2021-12-28'])->with(['detail'])->get();

		if ( $d_kirim_pakan->count() > 0 ) {
			$d_kirim_pakan = $d_kirim_pakan->toArray();
			foreach ($d_kirim_pakan as $k_kp => $v_kp) {
				$m_terima_pakan = new \Model\Storage\TerimaPakan_model();
				$d_terima_pakan = $m_terima_pakan->where('id_kirim_pakan', $v_kp['id'])->first();

				if ( !$d_terima_pakan ) {
					$m_terima_pakan = new \Model\Storage\TerimaPakan_model();
                    $now = $m_terima_pakan->getDate();

                    $m_terima_pakan->id_kirim_pakan = $v_kp['id'];
                    $m_terima_pakan->tgl_trans = $now['waktu'];
                    $m_terima_pakan->tgl_terima = $v_kp['tgl_kirim'];
                    $m_terima_pakan->path = null;
                    $m_terima_pakan->save();

                    $id_header = $m_terima_pakan->id;

                    foreach ($v_kp['detail'] as $k_detail => $v_detail) {
                        $m_terima_pakan_detail = new \Model\Storage\TerimaPakanDetail_model();
                        $m_terima_pakan_detail->id_header = $id_header;
                        $m_terima_pakan_detail->item = $v_detail['item'];
                        $m_terima_pakan_detail->jumlah = $v_detail['jumlah'];
                        $m_terima_pakan_detail->kondisi = $v_detail['kondisi'];
                        $m_terima_pakan_detail->save();
                    }

                    $d_terima_pakan = $m_terima_pakan->where('id', $id_header)->first();

                    $deskripsi_log_terima_pakan = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                    Modules::run( 'base/event/save', $d_terima_pakan, $deskripsi_log_terima_pakan);
				}
			}
		}
	}

	public function insert_saldo_pelanggan()
	{
		$arr = array(
			array('Slamet Wahyudi', 'BANYUWANGI', '22930000'),
			array('Suhardi', 'BANYUWANGI', '36540900'),
			array('Mohamad Eko Faris Azhar', 'BANYUWANGI', '29419300'),
			array('Stefanus Tomy Kurniawan', 'BANYUWANGI', '11910000'),
			array('Siti Aisah', 'JEMBER', '29542750'),
			array('Umi Lutfiani Masithah', 'BONDOWOSO', '1950'),
			array('Chotimah', 'JEMBER', '2650'),
			array('Kasmu', 'JEMBER', '18559050'),
			array('Rani Sanjaya', 'LUMAJANG', '354216350'),
			array('Suwito', 'JEMBER', '16500'),
			array('Ahmadi', 'JEMBER', '26692950'),
			array('Abi Abdillah', 'JEMBER', '500'),
			array('Khoirul Anam', 'LUMAJANG', '28960750'),
			array('Yeni Astria Ningsih', 'JEMBER', '6650'),
			array('Susiyanah', 'JEMBER', '13800'),
			array('Tonny Kiantara', 'JEMBER', '1550'),
			array('Umi Ajijah', 'JEMBER', '6250'),
			array('Imam Mustofa', 'JEMBER', '250'),
			array('Yaman Didik Hariyanto', 'JEMBER', '24345'),
			array('Nanok Sismianto', 'JEMBER', '17454650'),
			array('Tedy Kumala', 'BONDOWOSO', '500'),
			array('Rismidarliah', 'LUMAJANG', '1000'),
			array('Ruyantoh', 'JEMBER', '50'),
			array('M.Munir Adi Prayoga', 'JEMBER', '30864000'),
			array('Didik Sutrisno', 'JEMBER', '450'),
			array('Wawan Hidayatulloh', 'JEMBER', '11982250'),
			array('M.Irsyadul Ibat', 'JEMBER', '43230000'),
			array('Sumarni B Yeni', 'JEMBER', '41097250'),
			array('Muhammad Munir', 'PASURUAN', '30000'),
			array('Ngateno', 'LUMAJANG', '25088150'),
			array('M. Mansur', 'JEMBER', '100'),
			array('Karyono', 'LUMAJANG', '1000'),
			array('Johannes', 'JEMBER', '2000'),
			array('Miswanto', 'JEMBER', '45497000'),
			array('Achmad Faizal', 'BANYUWANGI', '32190000'),
			array('Muhammad Alim Muslim', 'LUMAJANG', '4260750'),
			array('Dewi Nur Kamila', 'SITUBONDO', '6790'),
			array('Bambang Brontoyono', 'PROBOLINGGO', '33719400'),
			array('Sahid', 'PROBOLINGGO', '6250'),
			array('Muhammad Fadloli', 'PASURUAN', '9690000'),
			array('Muhammad Hadar', 'PASURUAN', '11184100'),
			array('Ragil Alfan Hidayat', 'PASURUAN', '29100000'),
			array('Arif Budianto', 'MALANG', '2029500'),
			array('Muhammad Kurdi', 'MALANG', '800'),
			array('Sia Kok Ing', 'KOTA MALANG', '30000000'),
			array('Yulianto', 'MALANG', '500'),
			array('Ferdiansyah Deny Hartono', 'MALANG', '50'),
			array('Surono', 'MALANG', '57480250'),
			array('Ryan Andib Prayogo', 'MALANG', '250'),
			array('Achmad Zamzuri', 'JOMBANG', '37710'),
			array("Syafa'at", 'MOJOKERTO', '6330'),
			array('M. Yusufi', 'MOJOKERTO', '27454540'),
			array('Amelia Romadhini', 'MOJOKERTO', '11300270'),
			array('Miftahul Ulum', 'JOMBANG', '22480'),
			array('Qodirin', 'MOJOKERTO', '8660'),
			array('Wildhan Mubaroq', 'PASURUAN', '1800'),
			array('Samsul Huda', 'MOJOKERTO', '600'),
			array('Mohammad Imam Buchori', 'SIDOARJO', '250'),
			array('Agus Eko Saputro', 'MOJOKERTO', '100'),
			array('Indra Astutik', 'JOMBANG', '50'),
			array('Solikan', 'MOJOKERTO', '77200000'),
			array('Nur Arifin', 'KEDIRI', '900'),
			array('Mujiati', 'KEDIRI', '10400'),
			array('Sugiarto', 'KEDIRI', '50'),
			array('Agung Abdul Wachid', 'KOTA SURABAYA', '90533500'),
			array('Achmad Luluk Fathurahman', 'BLITAR', '100'),
			array('Anik', 'TULUNGAGUNG', '750'),
			array('Abu Sujak', 'TULUNGAGUNG', '1740'),
			array('Arik Herwanto', 'TULUNGAGUNG', '2150'),
			array('Panut Prasetyo', 'TULUNGAGUNG', '11700'),
			array('Yon Haryono', 'TULUNGAGUNG', '500'),
			array('Fatchur Roziq Mustofa Naim', 'TULUNGAGUNG', '50'),
			array('Darminto', 'TULUNGAGUNG', '9910'),
			array('Rochmad', 'KEDIRI', '600'),
			array('Joko Puji Kuswoyo', 'TULUNGAGUNG', '830'),
			array('Mat Suryan', 'KEDIRI', '50'),
			array('Nickoeris Setiawan', 'TULUNGAGUNG', '200'),
			array('Musiaman', 'TULUNGAGUNG', '3960'),
			array('Winarko', 'KEDIRI', '49804800'),
			array('Surip Abdul Qohar', 'KEDIRI', '22233600'),
			array('Anton Pratomo', 'KEDIRI', '10556200'),
			array('Suriyat, Drs', 'LAMONGAN', '54320410'),
			array('Abdul Munif', 'GRESIK', '900'),
			array('Syolikan Arif, ST', 'GRESIK', '23250'),
			array('Imam Thohari', 'LAMONGAN', '92269030'),
			array('Mudlofir', 'LAMONGAN', '2000'),
			array('Yudi Yuswanto', 'MOJOKERTO', '42478710'),
			array('Nur Komari', 'GRESIK', '9000'),
			array('Frans Pitrajaya', 'KOTA SURABAYA', '80'),
			array('Akhyatul Munir', 'GRESIK', '30649420'),
			array('Sumadi Maryanto', 'LAMONGAN', '250'),
			array('Sukri', 'LAMONGAN', '60'),
			array('Abdul Yusuf Faisal', 'KOTA SURABAYA', '71225000'),
			array('Istianah', 'LAMONGAN', '25584350'),
			array('Musri', 'MAGETAN', '1550'),
			array('Sarmi', 'MAGETAN', '100'),
			array('Edi Susanto', 'NGANJUK', '900'),
			array('Hariyanto', 'MAGETAN', '3000'),
			array('Sumadi', 'GARUM', '600'),
			array('Liem Tjhioe Giok', 'KOTA MADIUN', '200'),
			array('Ibnu Masngut', 'NGANJUK', '900'),
			array('Andi Setiawan', 'MALANG', '200'),
			array('M Irsyadul Munib', 'MAGETAN', '2300'),
			array('Arini Julaika', 'MAGETAN', '750'),
			array('Gunariyanto', 'NGAWI', '600'),
			array('Basuki', 'MAGETAN', '600'),
			array('Hasdi', 'NGANJUK', '15680'),
			array('Edo Maryoto', 'DAWARBLANDONG', '9637200'),
			array('Bejo Suroto', 'BOYOLALI', '1520'),
			array('Selvy Setyawati', 'BOYOLALI', '520'),
			array('Buniyati', 'KLATEN', '306020'),
			array('Sartono', 'KARANGANYAR', '400'),
			array('Yuhanus Dwi Sunaryo', 'DAWARBLANDONG', '540'),
			array('Suhadi', 'PROBOLINGGO', '1740'),
			array('Dedy Harnanto', 'BOYOLALI', '40'),
			array('Diah Ayu Ratna Wulandari', 'BOYOLALI', '400'),
			array('Widodo', 'SRAGEN', '20800'),
			array('Asih Yuniati', 'KOTA SURAKARTA', '800'),
			array('Suwadi', 'SEMARANG', '19156720'),
			array('Sri Darini DRA MSC', 'SUKOHARJO', '600'),
			array('Ika Sri Wahyuningsih', 'BOYOLALI', '2220'),
			array('Mukholis Nugroho', 'BOYOLALI', '380'),
			array('Seno Hantoro', 'BOYOLALI', '160'),
			array('Dwi Santoso', 'SUKOHARJO', '200 ')
		);

		foreach ($arr as $k_arr => $v_arr) {
			$m_plg = new \Model\Storage\Pelanggan_model();
			$d_plg = $m_plg->where('nama', 'like', '%'.strtoupper($v_arr[0]).'%')->where('tipe', 'pelanggan')->orderBy('version', 'desc')->first();

			$m_sld_plg = new \Model\Storage\SaldoPelanggan_model();
			$m_sld_plg->jenis_saldo = 'D';
			$m_sld_plg->no_pelanggan = $d_plg->nomor;
			$m_sld_plg->id_trans = null;
			$m_sld_plg->tgl_trans = '2021-11-01';
			$m_sld_plg->jenis_trans = 'pembayaran_pelanggan';
			$m_sld_plg->nominal = 0;
			$m_sld_plg->saldo = $v_arr[2];
			$m_sld_plg->save();
		}
	}
}