<?php defined('BASEPATH') or exit('No direct script access allowed');

class ListShift extends Public_Controller
{
    private $pathView = 'transaksi/list_shift/';
    private $url;
    private $hasAkses;
    // private $persen_ppn;
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hasAkses = hakAkses($this->url);
    }

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * Default
     */
    public function index()
    {
        // if ( $this->hasAkses['a_view'] == 1 ) {
            $this->load->library('Mobile_Detect');
            $detect = new Mobile_Detect();

            $this->add_external_js(
                array(
                    "assets/select2/js/select2.min.js",
                    'assets/transaksi/list_shift/js/list-shift.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/transaksi/list_shift/css/list-shift.css'
                )
            );
            $data = $this->includes;

            $content['akses'] = $this->hasAkses;

            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        // } else {
        //     showErrorAkses();
        // }
    }

    public function getLists()
    {
        $params = $this->input->get('params');

        $tanggal = $params['tanggal'];

        $start_date = $tanggal.' 00:00:00.001';
        $end_date = $tanggal.' 23:59:59.999';

        $m_conf = new \Model\Storage\Conf();
        $sql = "
            select 
                cs.*,
                mu.username_user as nama,
                case
                    when cs.id = max_cs.id then
                        1
                    else
                        0
                end as _delete
            from closing_shift cs 
            right join
                ms_user mu
                on
                    cs.user_id = mu.id_user
            left join
                (
                    select max(id) as id, user_id from closing_shift group by user_id
                ) max_cs
                on
                    cs.user_id = max_cs.user_id
            where 
                cs.tanggal between '".$start_date."' and '".$end_date."'
            order by
                cs.tanggal desc
        ";
        $d_cs = $m_conf->hydrateRaw( $sql );

        $data = null;
        if ( $d_cs->count() > 0 ) {
            $data = $d_cs->toArray();
        }

        $content['data'] = $data;
        $html = $this->load->view($this->pathView . 'list', $content, TRUE);

        echo $html;
    }

    public function modalListBayar()
    {
        $params = $this->input->post('params');

        try {
            $m_conf = new \Model\Storage\Conf();
            $now = $m_conf->getDate();
            $today = $now['tanggal'];

            $m_cs = new \Model\Storage\ClosingShift_model();
            $d_cs = $m_cs->where('id', $params['id'])->orderBy('tanggal', 'desc')->first();

            $kasir = $d_cs->user_id;

            $start_date = substr($today, 0, 10).' 00:00:00';
            $end_date = $today.' 23:59:59';
            if ( $d_cs ) {
                $d_cs_prev = $m_cs->where('user_id', $d_cs->user_id)->where('tanggal', '<', $d_cs->tanggal)->orderBy('tanggal', 'desc')->first();

                if ( $d_cs_prev ) {
                    $start_date = substr($d_cs_prev->tanggal, 0, 19);
                }

                $end_date = $d_cs->tanggal;
            }

            $m_jual = new \Model\Storage\Jual_model();
            $d_jual = $m_jual->whereBetween('tgl_trans', [$start_date, $end_date])->where('kasir', $kasir)->where('mstatus', 1)->with(['jual_item', 'jual_diskon', 'bayar'])->get();

            $data_bayar = ($d_jual->count() > 0) ? $this->getDataBayar($d_jual) : null;
            $data_belum_bayar = ($d_jual->count() > 0) ? $this->getDataBelumBayar($d_jual) : null;

            $content['data'] = array(
                'data_bayar' => $data_bayar,
                'data_belum_bayar' => $data_belum_bayar
            );
            $content['id_closing_shift'] = $params['id'];

            $html = $this->load->view($this->pathView . 'modal_list_bayar', $content, TRUE);
            
            $this->result['html'] = $html;
            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }

    public function getDataBayar($_data)
    {
        $data = null;
        foreach ($_data as $k_data => $v_data) {
            if ( $v_data['lunas'] == 1 ) {
                $total_bayar = 0;
                $jml_bayar = 0;
                if ( !empty($v_data['bayar']) ) {
                    foreach ($v_data['bayar'] as $k_bayar => $v_bayar) {
                        $total_bayar += $v_bayar['jml_tagihan'];
                        $jml_bayar++;
                    }
                }

                $data[ $v_data['kode_faktur'] ] = array(
                    'kode_faktur' => $v_data['kode_faktur'],
                    'pelanggan' => $v_data['member'],
                    'total' => $v_data['grand_total'],
                    'total_bayar' => $total_bayar,
                    'selisih_bayar' => ($total_bayar - $v_data['grand_total']),
                    'jml_bayar' => $jml_bayar,
                );
            }
        }

        return $data;
    }

    public function getDataBelumBayar($_data)
    {
        $data = null;
        foreach ($_data as $k_data => $v_data) {
            if ( $v_data['lunas'] == 0 ) {
                $kurang_bayar = 0;
                if ( !empty($v_data['bayar']) ) {
                    foreach ($v_data['bayar'] as $k_bayar => $v_bayar) {
                        $kurang_bayar += $v_bayar['jml_bayar'];
                    }
                }

                $data[ $v_data['kode_faktur'] ] = array(
                    'kode_faktur' => $v_data['kode_faktur'],
                    'pelanggan' => $v_data['member'],
                    'total' => $v_data['grand_total'],
                    'kurang_bayar' => $v_data['grand_total'] - $kurang_bayar
                );
            }
        }

        return $data;
    }

    public function getDataClosingShift($id_closing_shift)
    {
        $m_cs = new \Model\Storage\ClosingShift_model();
        $d_cs = $m_cs->where('id', $id_closing_shift)->orderBy('tanggal', 'desc')->first();

        $kasir = $d_cs->user_id;

        $d_cs_prev = $m_cs->where('tanggal', '<', $d_cs->tanggal)->where('user_id', $kasir)->orderBy('tanggal', 'desc')->first();

        $start_date = $d_cs_prev->tanggal;
        $end_date = $d_cs->tanggal;

        $m_jual = new \Model\Storage\Jual_model();
        $d_jual = $m_jual->whereBetween('tgl_trans', [$start_date, $end_date])->where('kasir', $kasir)->with(['jual_item', 'bayar'])->get();

        $data = null;
        $data_detail_transaksi = null;
        $data_detail_pembayaran = null;
        if ( $d_jual->count() > 0 ) {
            $d_jual = $d_jual->toArray();

            $data_detail_transaksi['grand_total'] = 0;
            $data_detail_transaksi['grand_total_jumlah'] = 0;
            $data_detail_pembayaran['grand_total'] = 0;

            $data_detail_transaksi['detail']['item_terjual']['nama'] = 'item terjual';
            $data_detail_transaksi['detail']['item_terjual']['jumlah'] = 0;
            $data_detail_transaksi['detail']['item_terjual']['total'] = 0;

            $data_detail_transaksi['detail']['item_belum_bayar']['nama'] = 'item belum bayar';
            $data_detail_transaksi['detail']['item_belum_bayar']['jumlah'] = 0;
            $data_detail_transaksi['detail']['item_belum_bayar']['total'] = 0;

            $data_detail_transaksi['detail']['item_batal']['nama'] = 'item batal';
            $data_detail_transaksi['detail']['item_batal']['jumlah'] = 0;
            $data_detail_transaksi['detail']['item_batal']['total'] = 0;
            foreach ($d_jual as $k_jual => $v_jual) {
                if ( !empty($v_jual['bayar']) ) {
                    foreach ($v_jual['bayar'] as $k_bayar => $v_bayar) {
                        if ( $v_jual['mstatus'] == 1 && $v_jual['lunas'] == 1 ) {
                            // if ( $v_bayar['jml_tagihan'] <= $v_bayar['jml_bayar'] ) {
                                if ( $v_bayar['jenis_bayar'] == 'tunai' ) {
                                    $urut = 0;
                                    $key_bayar = $v_bayar['jenis_bayar'];
                                    if ( !isset( $data_detail_pembayaran['detail'][ $urut ][ $key_bayar ] ) ) {
                                        $data_detail_pembayaran['detail'][ $urut ][ $key_bayar ] = array(
                                            'nama' => 'TUNAI',
                                            'bayar' => ($v_bayar['jml_tagihan'] < $v_bayar['jml_bayar']) ? $v_bayar['jml_tagihan'] : $v_bayar['jml_bayar'],
                                            'tagihan' => $v_bayar['jml_tagihan'],
                                            'kembalian' => $v_bayar['jml_bayar'] - $v_bayar['jml_tagihan']
                                        );
                                    } else {
                                        $data_detail_pembayaran['detail'][ $urut ][ $key_bayar ]['bayar'] += ($v_bayar['jml_tagihan'] < $v_bayar['jml_bayar']) ? $v_bayar['jml_tagihan'] : $v_bayar['jml_bayar'];
                                        $data_detail_pembayaran['detail'][ $urut ][ $key_bayar ]['tagihan'] += $v_bayar['jml_tagihan'];
                                        $data_detail_pembayaran['detail'][ $urut ][ $key_bayar ]['kembalian'] += $v_bayar['jml_bayar'] - $v_bayar['jml_tagihan'];
                                    }
                                } else {
                                    $urut = $v_bayar['jenis_kartu']['urut'];
                                    $key_bayar = $v_bayar['jenis_bayar'].' | '.$v_bayar['jenis_kartu_kode'];
                                    if ( !isset( $data_detail_pembayaran['detail'][ $urut ][ $key_bayar ] ) ) {
                                        $data_detail_pembayaran['detail'][ $urut ][ $key_bayar ] = array(
                                            'nama' => $v_bayar['jenis_kartu']['nama'],
                                            'bayar' => ($v_bayar['jml_tagihan'] < $v_bayar['jml_bayar']) ? $v_bayar['jml_tagihan'] : $v_bayar['jml_bayar']
                                        );
                                    } else {
                                        $data_detail_pembayaran['detail'][ $urut ][ $key_bayar ]['bayar'] += ($v_bayar['jml_tagihan'] < $v_bayar['jml_bayar']) ? $v_bayar['jml_tagihan'] : $v_bayar['jml_bayar'];
                                    }
                                }

                                $data_detail_pembayaran['grand_total'] += $v_bayar['jml_tagihan'];
                            // }
                        }
                    }

                    if ( isset($data_detail_pembayaran['detail']) ) {
                        ksort( $data_detail_pembayaran['detail'] );
                    }
                }

                foreach ($v_jual['jual_item'] as $k_ji => $v_ji) {
                    // LUNAS
                    if ( $v_jual['mstatus'] == 1 ) {
                        if ( !isset($data_detail_transaksi['detail']['item_terjual']['detail'][ $v_ji['menu_kode'] ]) ) {
                            $data_detail_transaksi['detail']['item_terjual']['detail'][ $v_ji['menu_kode'] ] = array(
                                'nama' => $v_ji['menu_nama'],
                                'jumlah' => $v_ji['jumlah'],
                                'total' => $v_ji['total']
                            );
                        } else {
                            $data_detail_transaksi['detail']['item_terjual']['detail'][ $v_ji['menu_kode'] ]['jumlah'] += $v_ji['jumlah'];
                            $data_detail_transaksi['detail']['item_terjual']['detail'][ $v_ji['menu_kode'] ]['total'] += $v_ji['total'];
                        }
                        $data_detail_transaksi['detail']['item_terjual']['jumlah'] += $v_ji['jumlah'];
                        $data_detail_transaksi['detail']['item_terjual']['total'] += $v_ji['total'];

                        $data_detail_transaksi['grand_total_jumlah'] += $v_ji['jumlah'];
                        $data_detail_transaksi['grand_total'] += $v_ji['total'];
                    }
                    // BELUM LUNAS
                    if ( $v_jual['mstatus'] == 1 && $v_jual['lunas'] == 0 ) {
                        if ( !isset($data_detail_transaksi['detail']['item_belum_bayar']['detail'][ $v_ji['menu_kode'] ]) ) {
                            $data_detail_transaksi['detail']['item_belum_bayar']['detail'][ $v_ji['menu_kode'] ] = array(
                                'nama' => $v_ji['menu_nama'],
                                'jumlah' => $v_ji['jumlah'],
                                'total' => $v_ji['total']
                            );
                        } else {
                            $data_detail_transaksi['detail']['item_belum_bayar']['detail'][ $v_ji['menu_kode'] ]['jumlah'] += $v_ji['jumlah'];
                            $data_detail_transaksi['detail']['item_belum_bayar']['detail'][ $v_ji['menu_kode'] ]['total'] += $v_ji['total'];
                        }
                        $data_detail_transaksi['detail']['item_belum_bayar']['jumlah'] += $v_ji['jumlah'];
                        $data_detail_transaksi['detail']['item_belum_bayar']['total'] += $v_ji['total'];

                        $data_detail_transaksi['grand_total_jumlah'] += $v_ji['jumlah'];
                        $data_detail_transaksi['grand_total'] += $v_ji['total'];
                    }
                    // BATAL
                    if ( $v_jual['mstatus'] == 0 ) {
                        if ( !isset($data_detail_transaksi['detail']['item_batal']['detail'][ $v_ji['menu_kode'] ]) ) {
                            $data_detail_transaksi['detail']['item_batal']['detail'][ $v_ji['menu_kode'] ] = array(
                                'nama' => $v_ji['menu_nama'],
                                'jumlah' => $v_ji['jumlah'],
                                'total' => $v_ji['total']
                            );
                        } else {
                            $data_detail_transaksi['detail']['item_batal']['detail'][ $v_ji['menu_kode'] ]['jumlah'] += $v_ji['jumlah'];
                            $data_detail_transaksi['detail']['item_batal']['detail'][ $v_ji['menu_kode'] ]['total'] += $v_ji['total'];
                        }
                        $data_detail_transaksi['detail']['item_batal']['jumlah'] += $v_ji['jumlah'];
                        $data_detail_transaksi['detail']['item_batal']['total'] += $v_ji['total'];
                    }
                }
            }
        }

        $data = array(
            'detail_transaksi' => $data_detail_transaksi,
            'detail_pembayaran' => $data_detail_pembayaran
        );

        return $data;
    }

    public function printClosingShift()
    {
        $params = $this->input->post('params');

        $id_closing_shift = $params['id']; 

        if ( $this->config->item('paper_size') == '58' ) {
            $result = $this->printClosingShift58($id_closing_shift);
        } else {
            $result = $this->printClosingShift80($id_closing_shift);
        }

        display_json( $result );
    }

    public function printClosingShift58($id_closing_shift)
    {
        try {
            $conf = new \Model\Storage\Conf();
            $now = $conf->getDate();

            $data = $this->getDataClosingShift( $id_closing_shift );

            $m_cs = new \Model\Storage\ClosingShift_model();
            $d_cs = $m_cs->where('id', $id_closing_shift)->with(['user'])->orderBy('tanggal', 'desc')->first();
            $d_cs_prev = $m_cs->where('tanggal', '<', $d_cs->tanggal)->where('user_id', $d_cs->user_id)->with(['user'])->orderBy('tanggal', 'desc')->first();

            $kasir = $d_cs_prev->user_id;
            $start_date = $d_cs_prev->tanggal;
            $end_date = $d_cs->tanggal;

            // cetak_r($start_date.' | '.$end_date, 1);

            $tgl_print = $d_cs->tanggal;
            $nama_user = $d_cs->user->username_user;

            // Enter the share name for your USB printer here
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');
            // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $printer -> setJustification(0);
            $printer -> selectPrintMode(1);
            $printer -> setTextSize(2, 1);
            $printer -> text("LAPORAN SHIFT\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> selectPrintMode(1);
            $lineNoTransaksi = sprintf('%-13s %1.05s %-15s','Kasir',':', $nama_user);
            $printer -> text("$lineNoTransaksi\n");
            $lineKasir = sprintf('%-13s %1.05s %-15s','Tanggal',':', $tgl_print);
            $printer -> text("$lineKasir\n");
            $lineBranch = sprintf('%-13s %1.05s %-15s','Outlet',':', $this->kodebranch);
            $printer -> text("$lineBranch\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> selectPrintMode(1);
            $printer -> setTextSize(2, 1);
            $printer -> textRaw("\nDETAIL TRANSAKSI\n");
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("================================\n");

            foreach ($data['detail_transaksi']['detail'] as $k_data => $v_dt) {
                $total = 0;
                $jumlah = 0;

                $printer -> setJustification(0);
                $printer -> selectPrintMode(1);
                $printer -> textRaw(strtoupper($v_dt['nama'])."\n");

                if ( isset($v_dt['detail']) ) {
                    foreach ($v_dt['detail'] as $k_det => $v_det) {
                        $line1 = sprintf('%-28s %13.40s', $v_det['nama'], '');
                        $printer -> text("$line1\n");
                        $line2 = sprintf('%-28s %13.40s', angkaRibuan($v_det['jumlah']), angkaDecimal($v_det['total']));
                        $printer -> text("$line2\n");

                        $total += $v_det['total'];
                        $jumlah += $v_det['jumlah'];
                    }

                    $printer -> setJustification(1);
                    $printer -> selectPrintMode(8);
                    $printer -> text("--------------------------------\n");
                    $printer -> setJustification(0);
                    $printer -> selectPrintMode(1);
                    $line_total = sprintf('%28s %13.40s', 'TOTAL ('.angkaRibuan($jumlah).')', angkaDecimal($total));
                    $printer -> text("$line_total\n");
                }

                $printer -> textRaw("\n");
            }

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("--------------------------------\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(2);
            $printer -> selectPrintMode(1);
            $lineGrandTotal = sprintf('%28s %13.40s','GRAND TOTAL ('.angkaRibuan($data['detail_transaksi']['grand_total_jumlah']).')', angkaDecimal($data['detail_transaksi']['grand_total']));
            $printer -> text("$lineGrandTotal\n\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> selectPrintMode(1);
            $printer -> setTextSize(2, 1);
            $printer -> textRaw("\nDETAIL PEMBAYARAN\n");
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("================================\n");

            foreach ($data['detail_pembayaran']['detail'] as $k_dp => $v_dp) {
                foreach ($v_dp as $k_urut => $v_urut) {
                    $printer -> setJustification(0);
                    $printer -> selectPrintMode(1);

                    $bayar = $v_urut['bayar'];
                    $kembalian = isset($v_urut['kembalian']) ? $v_urut['kembalian'] : 0;
                    // $nilai = $bayar - $kembalian;
                    $nilai = $bayar;

                    $line = sprintf('%-46s %13.40s', strtoupper($v_urut['nama']), angkaDecimal($nilai));
                    $printer -> text("$line\n");

                    // if ( stristr($k_dp, 'tunai') !== FALSE ) {
                    //     // $printer -> textRaw(strtoupper($v_dp['nama'])."\n");

                    //     // if ( isset($v_dp) ) {
                    //     //     foreach ($v_dp as $k_det => $v_det) {
                    //     //         if ( stristr($k_det, 'nama') === FALSE ) {
                    //     //             $line = sprintf('%-28s %13.40s', strtoupper($k_det), angkaDecimal($v_det));
                    //     //             $printer -> text("$line\n");
                    //     //         }
                    //     //     }
                    //     // }
                    //     $line = sprintf('%-28s %13.40s', strtoupper($v_urut['nama']), angkaDecimal($v_urut['tagihan']));
                    //     $printer -> text("$line\n");
                    // } else {
                    //     $line = sprintf('%-28s %13.40s', strtoupper($v_urut['nama']), angkaDecimal($v_urut['bayar']));
                    //     $printer -> text("$line\n");
                    // }

                    $printer -> textRaw("\n");
                }
            }

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("--------------------------------\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(2);
            $printer -> selectPrintMode(1);
            $lineGrandTotal = sprintf('%28s %13.40s','GRAND TOTAL', angkaDecimal($data['detail_pembayaran']['grand_total']));
            $printer -> text("$lineGrandTotal\n\n");

            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        return $this->result;
    }

    public function printClosingShift80($id_closing_shift)
    {
        try {
            $conf = new \Model\Storage\Conf();
            $now = $conf->getDate();

            $data = $this->getDataClosingShift( $id_closing_shift );

            $m_cs = new \Model\Storage\ClosingShift_model();
            $d_cs = $m_cs->where('id', $id_closing_shift)->with(['user'])->orderBy('tanggal', 'desc')->first();
            $d_cs_prev = $m_cs->where('tanggal', '<', $d_cs->tanggal)->where('user_id', $d_cs->user_id)->with(['user'])->orderBy('tanggal', 'desc')->first();

            $kasir = $d_cs_prev->user_id;
            $start_date = $d_cs_prev->tanggal;
            $end_date = $d_cs->tanggal;

            // cetak_r($start_date.' | '.$end_date, 1);

            $tgl_print = $d_cs->tanggal;
            $nama_user = $d_cs->user->username_user;

            // Enter the share name for your USB printer here
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');
            // $computer_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            // $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('smb://'.$computer_name.'/kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $printer -> setJustification(0);
            $printer -> selectPrintMode(1);
            $printer -> setTextSize(2, 1);
            $printer -> text("LAPORAN SHIFT\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> selectPrintMode(1);
            $lineNoTransaksi = sprintf('%-13s %1.05s %-15s','Kasir',':', $nama_user);
            $printer -> text("$lineNoTransaksi\n");
            $lineKasir = sprintf('%-13s %1.05s %-15s','Tanggal',':', $tgl_print);
            $printer -> text("$lineKasir\n");
            $lineBranch = sprintf('%-13s %1.05s %-15s','Outlet',':', $this->kodebranch);
            $printer -> text("$lineBranch\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> selectPrintMode(1);
            $printer -> setTextSize(2, 1);
            $printer -> textRaw("\nDETAIL TRANSAKSI\n");
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("==========================================\n");

            foreach ($data['detail_transaksi']['detail'] as $k_data => $v_dt) {
                $total = 0;
                $jumlah = 0;

                $printer -> setJustification(0);
                $printer -> selectPrintMode(1);
                $printer -> textRaw(strtoupper($v_dt['nama'])."\n");

                if ( isset($v_dt['detail']) ) {
                    foreach ($v_dt['detail'] as $k_det => $v_det) {
                        $line1 = sprintf('%-46s %13.40s', $v_det['nama'], '');
                        $printer -> text("$line1\n");
                        $line2 = sprintf('%-46s %13.40s', angkaRibuan($v_det['jumlah']), angkaDecimal($v_det['total']));
                        $printer -> text("$line2\n");

                        $total += $v_det['total'];
                        $jumlah += $v_det['jumlah'];
                    }

                    $printer -> setJustification(1);
                    $printer -> selectPrintMode(8);
                    $printer -> text("------------------------------------------\n");
                    $printer -> setJustification(0);
                    $printer -> selectPrintMode(1);
                    $line_total = sprintf('%46s %13.40s', 'TOTAL ('.angkaRibuan($jumlah).')', angkaDecimal($total));
                    $printer -> text("$line_total\n");
                }

                $printer -> textRaw("\n");
            }

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("------------------------------------------\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(2);
            $printer -> selectPrintMode(1);
            $lineGrandTotal = sprintf('%46s %13.40s','GRAND TOTAL ('.angkaRibuan($data['detail_transaksi']['grand_total_jumlah']).')', angkaDecimal($data['detail_transaksi']['grand_total']));
            $printer -> text("$lineGrandTotal\n\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> selectPrintMode(1);
            $printer -> setTextSize(2, 1);
            $printer -> textRaw("\nDETAIL PEMBAYARAN\n");
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("==========================================\n");

            foreach ($data['detail_pembayaran']['detail'] as $k_dp => $v_dp) {
                foreach ($v_dp as $k_urut => $v_urut) {
                    $printer -> setJustification(0);
                    $printer -> selectPrintMode(1);

                    $bayar = $v_urut['bayar'];
                    $kembalian = isset($v_urut['kembalian']) ? $v_urut['kembalian'] : 0;
                    $nilai = $bayar - $kembalian;

                    $line = sprintf('%-46s %13.40s', strtoupper($v_urut['nama']), angkaDecimal($nilai));
                    $printer -> text("$line\n");

                    // if ( stristr($k_dp, 'tunai') !== FALSE ) {
                    //     // $printer -> textRaw(strtoupper($v_dp['nama'])."\n");

                    //     // if ( isset($v_dp) ) {
                    //     //     foreach ($v_dp as $k_det => $v_det) {
                    //     //         if ( stristr($k_det, 'nama') === FALSE ) {
                    //     //             $line = sprintf('%-28s %13.40s', strtoupper($k_det), angkaDecimal($v_det));
                    //     //             $printer -> text("$line\n");
                    //     //         }
                    //     //     }
                    //     // }
                    //     $line = sprintf('%-46s %13.40s', strtoupper($v_urut['nama']), angkaDecimal($v_urut['tagihan']));
                    //     $printer -> text("$line\n");
                    // } else {
                    //     $line = sprintf('%-46s %13.40s', strtoupper($v_urut['nama']), angkaDecimal($v_urut['bayar']));
                    //     $printer -> text("$line\n");
                    // }

                    $printer -> textRaw("\n");
                }
            }

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("------------------------------------------\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(2);
            $printer -> selectPrintMode(1);
            $lineGrandTotal = sprintf('%28s %13.40s','GRAND TOTAL', angkaDecimal($data['detail_pembayaran']['grand_total']));
            $printer -> text("$lineGrandTotal\n\n");

            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        return $this->result;
    }

    public function delete() {
        $params = $this->input->post('params');

        try {
            $id = $params['id'];

            $m_cs = new \Model\Storage\ClosingShift_model();
            $m_cs->where('id', $id)->delete();

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }
}