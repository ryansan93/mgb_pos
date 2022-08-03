<?php defined('BASEPATH') or exit('No direct script access allowed');

class Penjualan extends Public_Controller
{
    private $pathView = 'transaksi/penjualan/';
    private $url;
    private $hakAkses;
    private $persen_ppn = 0;
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
                    'assets/transaksi/penjualan/js/penjualan.js'
                )
            );
            $this->add_external_css(
                array(
                    "assets/select2/css/select2.min.css",
                    'assets/transaksi/penjualan/css/penjualan.css'
                )
            );
            $data = $this->includes;

            $isMobile = true;
            if ( $detect->isMobile() ) {
                $isMobile = true;
            }

            $content['akses'] = $this->hasAkses;
            $content['isMobile'] = $isMobile;
            $content['persen_ppn'] = $this->persen_ppn;

            $content['kategori'] = $this->getKategori();

            $data['view'] = $this->load->view($this->pathView . 'index', $content, TRUE);

            $this->load->view($this->template, $data);
        // } else {
        //     showErrorAkses();
        // }
    }

    public function getJenisPesanan()
    {
        $m_jp = new \Model\Storage\JenisPesanan_model();
        $d_jp = $m_jp->get();

        $data = null;
        if ( $d_jp->count() > 0 ) {
            $data = $d_jp->toArray();
        }

        return $data;
    }

    public function modalJenisPesanan()
    {
        $content['jenis_pesanan'] = $this->getJenisPesanan();

        $html = $this->load->view($this->pathView . 'modal_jenis_pesanan', $content, TRUE);

        echo $html;
    }

    public function modalPilihMember()
    {
        $html = $this->load->view($this->pathView . 'modal_pilih_member', null, TRUE);

        echo $html;
    }

    public function modalNonMember()
    {
        $html = $this->load->view($this->pathView . 'modal_non_member', null, TRUE);

        echo $html;
    }

    public function modalMember()
    {
        $m_member = new \Model\Storage\Member_model();
        $d_member = $m_member->get();

        $data = null;
        if ( $d_member->count() > 0 ) {
            $data = $d_member->toArray();
        }

        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'modal_member', $content, TRUE);

        echo $html;
    }

    public function addMember()
    {
        $html = $this->load->view($this->pathView . 'add_member', null, TRUE);

        echo $html;
    }

    public function saveMember()
    {
        $params = $this->input->post('params');
        try {
            $m_member = new \Model\Storage\Member_model();

            $kode_member = $m_member->getNextId();

            $m_member->kode_member = $kode_member;
            $m_member->nama = $params['nama'];
            $m_member->no_telp = $params['no_telp'];
            $m_member->alamat = $params['alamat'];
            $m_member->save();

            $d_member = $m_member->where('kode_member', $kode_member)->first()->toArray();

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_member, $deskripsi_log );
            
            $this->result['status'] = 1;
            $this->result['message'] = 'Data member berhasil di simpan.';
            $this->result['content'] = array(
                                            'kode_member' => $d_member['kode_member'],
                                            'nama' => $d_member['nama'],
                                        );
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function getKategori()
    {
        $m_kategori_menu = new \Model\Storage\KategoriMenu_model();
        $d_kategori_menu = $m_kategori_menu->orderBy('nama', 'asc')->get();

        $data = null;
        if ( $d_kategori_menu->count() > 0 ) {
            $data = $d_kategori_menu->toArray();
        }

        return $data;
    }

    public function getMenu()
    {
        $id_kategori = $this->input->get('id_kategori');
        $jenis_pesanan = $this->input->get('jenis_pesanan');

        $m_menu = new \Model\Storage\Menu_model();
        $sql = "
            select menu.id, menu.kode_menu, menu.nama, menu.deskripsi, hm.harga as harga_jual, menu.kategori_menu_id, count(pm.kode_paket_menu) as jml_paket from menu menu
                left join
                    (
                    select * from harga_menu where id in (
                        select max(id) as id from harga_menu group by jenis_pesanan_kode, menu_kode
                    )) hm 
                    on
                        menu.kode_menu = hm.menu_kode 
                left join
                    paket_menu pm
                    on
                        menu.kode_menu = pm.menu_kode
                where
                    menu.kategori_menu_id = ".$id_kategori." and
                    hm.jenis_pesanan_kode = '".$jenis_pesanan."'
            group by menu.id, menu.kode_menu, menu.nama, menu.deskripsi, hm.harga, menu.kategori_menu_id, hm.jenis_pesanan_kode
            order by menu.nama asc
        ";
        $d_menu = $m_menu->hydrateRaw($sql);

        $data = null;
        if ( $d_menu->count() > 0 ) {
            $data = $d_menu->toArray();
        }

        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'list_menu', $content, TRUE);

        echo $html;
    }

    public function modalPaketMenu()
    {
        $menu_kode = $this->input->get('menu_kode');

        $m_pm = new \Model\Storage\PaketMenu_model();
        $d_pm = $m_pm->where('menu_kode', $menu_kode)->with(['isi_paket_menu'])->get();

        $data = null;
        if ( $d_pm->count() > 0 ) {
            $data = $d_pm->toArray();
        }

        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'modal_paket_menu', $content, TRUE);

        echo $html;
    }

    public function jumlahPesanan()
    {
        $html = $this->load->view($this->pathView . 'jumlah_pesanan', null, TRUE);

        echo $html;
    }

    public function modalDiskon()
    {
        $kode_member = $this->input->get('kode_member');

        $today = date('Y-m-d');

        $m_diskon = new \Model\Storage\Diskon_model();
        $d_diskon = $m_diskon->where('start_date', '<=', $today)->where('end_date', '>=', $today)->with(['detail'])->get();

        $data = null;
        if ( $d_diskon->count() > 0 ) {
            $d_diskon = $d_diskon->toArray();
            foreach ($d_diskon as $key => $value) {
                foreach ($value['detail'] as $k_det => $v_det) {
                    if ( !empty($kode_member) ) {
                        if ( $v_det['member'] == 1 ) {
                            $data[] = $d_diskon[$key];
                        }
                    } else  {
                        if ( $v_det['non_member'] == 1 ) {
                            $data[] = $d_diskon[$key];
                        }
                    }
                }
            }
        }

        $content['data'] = $data;

        $html = $this->load->view($this->pathView . 'modal_diskon', $content, TRUE);

        echo $html;
    }

    public function savePenjualan()
    {
        $params = $this->input->post('params');

        try {
            $m_jual = new \Model\Storage\Jual_model();
            $now = $m_jual->getDate();

            $kode_faktur = $m_jual->getNextKode($this->kodebranch);
            $m_jual->kode_faktur = $kode_faktur;
            $m_jual->tgl_trans = $now['waktu'];
            $m_jual->branch = $this->kodebranch;
            $m_jual->member = $params['member'];
            $m_jual->kode_member = $params['kode_member'];
            $m_jual->kasir = $this->userid;
            $m_jual->nama_kasir = $this->userdata['detail_user']['nama_detuser'];
            $m_jual->total = $params['sub_total'];
            $m_jual->diskon = $params['diskon'];
            $m_jual->ppn = $params['ppn'];
            $m_jual->grand_total = $params['grand_total'];
            $m_jual->lunas = 0;
            $m_jual->mstatus = 1;
            $m_jual->save();

            foreach ($params['list_pesanan'] as $k_lp => $v_lp) {
                foreach ($v_lp['list_menu'] as $k_lm => $v_lm) {
                    $m_juali = new \Model\Storage\JualItem_model();

                    $kode_faktur_item = $m_juali->getNextKode('FKI');
                    $m_juali->kode_faktur_item = $kode_faktur_item;
                    $m_juali->faktur_kode = $kode_faktur;
                    $m_juali->kode_jenis_pesanan = $v_lp['kode_jp'];
                    $m_juali->menu_nama = $v_lm['nama_menu'];
                    $m_juali->menu_kode = $v_lm['kode_menu'];
                    $m_juali->jumlah = $v_lm['jumlah'];
                    $m_juali->harga = $v_lm['harga'];
                    $m_juali->total = $v_lm['total'];
                    $m_juali->save();

                    if ( !empty($v_lm['detail_menu']) ) {
                        foreach ($v_lm['detail_menu'] as $k_dm => $v_dm) {
                            $m_jualid = new \Model\Storage\JualItemDetail_model();
                            $m_jualid->faktur_item_kode = $kode_faktur_item;
                            $m_jualid->menu_nama = $v_dm['nama_menu'];
                            $m_jualid->menu_kode = $v_dm['kode_menu'];
                            $m_jualid->jumlah = $v_dm['jumlah'];
                            $m_jualid->save();
                        }
                    }
                }
            }

            if ( !empty($params['list_diskon']) ) {
                foreach ($params['list_diskon'] as $k_ld => $v_ld) {
                    $m_juald = new \Model\Storage\JualDiskon_model();
                    $m_juald->faktur_kode = $kode_faktur;
                    $m_juald->diskon_kode = $v_ld['kode_diskon'];
                    $m_juald->diskon_nama = $v_ld['nama_diskon'];
                    $m_juald->save();
                }
            }

            $deskripsi_log_gaktifitas = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_jual, $deskripsi_log_gaktifitas );
            
            $this->result['status'] = 1;
            $this->result['content'] = array('kode_faktur' => $kode_faktur);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function deletePenjualan()
    {
        $params = $this->input->post('params');

        try {
            $m_jual = new \Model\Storage\Jual_model();
            $m_jual->where('kode_faktur', $params)->update(
                array(
                    'mstatus' => 0
                )
            );

            $d_jual = $m_jual->where('kode_faktur', $params)->first();
            
            $deskripsi_log_gaktifitas = 'di-delete oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/update', $d_jual, $deskripsi_log_gaktifitas );

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function modalPilihBayar()
    {
        $content['data'] = null;

        $html = $this->load->view($this->pathView . 'modal_pilih_bayar', $content, TRUE);

        echo $html;
    }

    public function modalPembayaran()
    {
        $content['data'] = null;

        $html = $this->load->view($this->pathView . 'modal_pembayaran', $content, TRUE);

        echo $html;
    }

    public function modalJenisKartu()
    {
        $m_jenis_kartu = new \Model\Storage\JenisKartu_model();
        $_d_jenis_kartu = $m_jenis_kartu->where('status', 1)->get();

        $d_jenis_kartu = null;
        if ( $_d_jenis_kartu->count() > 0 ) {
            $d_jenis_kartu = $_d_jenis_kartu->toArray();
        }

        $content['data'] = $d_jenis_kartu;

        $html = $this->load->view($this->pathView . 'modal_jenis_kartu', $content, TRUE);

        echo $html;
    }

    public function jumlahBayar()
    {
        $html = $this->load->view($this->pathView . 'jumlah_bayar', null, TRUE);

        echo $html;
    }

    public function noBuktiKartu()
    {
        $html = $this->load->view($this->pathView . 'no_bukti_kartu', null, TRUE);

        echo $html;
    }

    public function savePembayaran()
    {
        $params = $this->input->post('params');

        try {
            $m_bayar = new \Model\Storage\Bayar_model();
            $now = $m_bayar->getDate();

            $m_bayar->tgl_trans = $now['waktu'];
            $m_bayar->faktur_kode = $params['faktur_kode'];
            $m_bayar->jml_tagihan = $params['jml_tagihan'];
            $m_bayar->jml_bayar = $params['jml_bayar'];
            $m_bayar->jenis_bayar = $params['jenis_bayar'];
            $m_bayar->jenis_kartu_kode = $params['jenis_kartu_kode'];
            $m_bayar->no_bukti = $params['no_bukti'];
            $m_bayar->save();

            if ( $params['jml_bayar'] >= $params['jml_tagihan'] ) {
                $m_jual = new \Model\Storage\Jual_model();
                $m_jual->where('kode_faktur', $params['faktur_kode'])->update(
                    array(
                        'lunas' => 1
                    )
                );
            }

            $deskripsi_log_gaktifitas = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run( 'base/event/save', $m_bayar, $deskripsi_log_gaktifitas );
            
            $this->result['status'] = 1;
            // $this->result['content'] = array('data' => $data);
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function getDataNota($kode_faktur)
    {
        $m_jual = new \Model\Storage\Jual_model();
        $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->with(['jual_item', 'bayar'])->first()->toArray();

        $data = null;
        $jenis_pesanan = null;
        foreach ($d_jual['jual_item'] as $k_ji => $v_ji) {  
            $key = $v_ji['jenis_pesanan'][0]['nama'].' | '.$v_ji['jenis_pesanan'][0]['kode'];
            $key_item = $v_ji['menu_nama'].' | '.$v_ji['menu_kode'];

            if ( !isset($jenis_pesanan[$key]) ) {
                $jual_item = null;
                $jual_item[ $key_item ] = array(
                    'nama' => $v_ji['menu_nama'],
                    'jumlah' => $v_ji['jumlah'],
                    'total' => $v_ji['total']
                );

                $jenis_pesanan[$key] = array(
                    'nama' => $v_ji['jenis_pesanan'][0]['nama'],
                    'jual_item' => $jual_item
                );
            } else {
                if ( !isset($jenis_pesanan[$key]['jual_item'][$key_item]) ) {
                    $jenis_pesanan[$key]['jual_item'][$key_item] = array(
                        'nama' => $v_ji['menu_nama'],
                        'jumlah' => $v_ji['jumlah'],
                        'total' => $v_ji['total']
                    );
                } else {
                    $jenis_pesanan[$key]['jual_item'][$key_item]['jumlah'] += $v_ji['jumlah'];
                    $jenis_pesanan[$key]['jual_item'][$key_item]['total'] += $v_ji['total'];
                }
            }
        }

        $data = array(
            'kode_faktur' => $d_jual['kode_faktur'],
            'tgl_trans' => $d_jual['tgl_trans'],
            'member' => $d_jual['member'],
            'kode_member' => $d_jual['kode_member'],
            'total' => $d_jual['total'],
            'diskon' => $d_jual['diskon'],
            'ppn' => $d_jual['ppn'],
            'grand_total' => $d_jual['grand_total'],
            'lunas' => $d_jual['lunas'],
            'jenis_pesanan' => $jenis_pesanan,
            'bayar' => $d_jual['bayar']
        );

        return $data;
    }

    public function getDataCheckList($kode_faktur)
    {
        $m_jual = new \Model\Storage\Jual_model();
        $d_jual = $m_jual->where('kode_faktur', $kode_faktur)->with(['jual_item', 'bayar'])->first()->toArray();

        $data = null;
        $jenis_pesanan = null;
        foreach ($d_jual['jual_item'] as $k_ji => $v_ji) {  
            $key = $v_ji['jenis_pesanan'][0]['nama'].' | '.$v_ji['jenis_pesanan'][0]['kode'];
            $key_item = $v_ji['kode_faktur_item'].' | '.$v_ji['menu_nama'].' | '.$v_ji['menu_kode'];

            $jual_item_detail = null;
            foreach ($v_ji['jual_item_detail'] as $k_jid => $v_jid) {
                $jual_item_detail[ $v_jid['menu_kode'] ] = array(
                    'menu_kode' => $v_jid['menu_kode'],
                    'menu_nama' => $v_jid['menu_nama']
                );
            }

            if ( !isset($jenis_pesanan[$key]) ) {
                $jual_item = null;
                $jual_item[ $key_item ] = array(
                    'nama' => $v_ji['menu_nama'],
                    'jumlah' => $v_ji['jumlah'],
                    'total' => $v_ji['total'],
                    'detail' => $jual_item_detail
                );

                $jenis_pesanan[$key] = array(
                    'nama' => $v_ji['jenis_pesanan'][0]['nama'],
                    'jual_item' => $jual_item
                );
            } else {
                if ( !isset($jenis_pesanan[$key]['jual_item'][$key_item]) ) {
                    $jenis_pesanan[$key]['jual_item'][$key_item] = array(
                        'nama' => $v_ji['menu_nama'],
                        'jumlah' => $v_ji['jumlah'],
                        'total' => $v_ji['total'],
                        'detail' => $jual_item_detail
                    );
                } else {
                    $jenis_pesanan[$key]['jual_item'][$key_item]['jumlah'] += $v_ji['jumlah'];
                    $jenis_pesanan[$key]['jual_item'][$key_item]['total'] += $v_ji['total'];
                }
            }
        }

        $data = array(
            'kode_faktur' => $d_jual['kode_faktur'],
            'tgl_trans' => $d_jual['tgl_trans'],
            'member' => $d_jual['member'],
            'kode_member' => $d_jual['kode_member'],
            'total' => $d_jual['total'],
            'diskon' => $d_jual['diskon'],
            'ppn' => $d_jual['ppn'],
            'grand_total' => $d_jual['grand_total'],
            'lunas' => $d_jual['lunas'],
            'jenis_pesanan' => $jenis_pesanan,
            'bayar' => $d_jual['bayar']
        );

        return $data;
    }

    public function printNota()
    {
        // $params = json_decode($this->input->post('params'), 1);
        $params = $this->input->post('params');

        try {
            $data = $this->getDataNota( $params );

            // Enter the share name for your USB printer here
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $printer -> setJustification(1);
            $printer -> selectPrintMode(32);
            $printer -> setTextSize(2, 1);
            $printer -> text("COD\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("FRIED CHICKEN\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            $printer -> text($this->alamatbranch."\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            $printer -> text("Telp. ".$this->telpbranch."\n\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(0);
            $printer -> selectPrintMode(1);
            $lineNoTransaksi = sprintf('%5.40s %1.05s %13.40s','No. Transaksi',':', $data['kode_faktur']);
            $printer -> text("$lineNoTransaksi\n");
            $lineKasir = sprintf('%-13.5s %1.05s %-13.40s','Kasir',':', $this->userdata['detail_user']['nama_detuser']);
            $printer -> text("$lineKasir\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("================================\n");
            // $printer -> textRaw("--------------------------------\n");
            foreach ($data['jenis_pesanan'] as $k_jp => $v_jp) {
                // $printer = new Mike42\Escpos\Printer($connector);
                $printer -> setJustification(0);
                $printer -> selectPrintMode(1);
                $printer -> textRaw($v_jp['nama']."\n");

                foreach ($v_jp['jual_item'] as $k_ji => $v_ji) {
                    /* NOTE : TABLE
                    $line = sprintf('%-13.40s %3.0f %-3.40s %9.40s %-2.40s %13.40s',$row['item_code'] , $row['item_qty'], $row['kali'], $n1,$row['hasil'], $n2); 
                    */
                    $line = sprintf('%-28s %13.40s',$v_ji['nama'].' @ '.angkaRibuan($v_ji['jumlah']), angkaDecimal($v_ji['total']));
                    $printer -> text("$line\n");
                }
            }
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("--------------------------------\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(2);
            $printer -> selectPrintMode(1);
            $lineTotal = sprintf('%18s %13.40s','Total Belanja. =', angkaDecimal($data['total']));
            $printer -> text("$lineTotal\n");
            // $lineTotal = sprintf('%18s %13.40s','PPN (11%).','=', angkaDecimal($data['ppn']));
            // $printer -> text("$lineTotal\n");
            $lineDisc = sprintf('%18s %13.40s','Disc. =', '('.angkaDecimal($data['diskon']).')');
            $printer -> text("$lineDisc\n");
            $lineTotal = sprintf('%18s %13.40s','Total Bayar. =', angkaDecimal($data['grand_total']));
            $printer -> text("$lineTotal\n");
            $lineTunai = sprintf('%18s %13.40s','Uang Tunai. =', angkaDecimal($data['bayar'][ count($data['bayar']) -1 ]['jml_bayar']));
            $printer -> text("$lineTunai\n");
            $lineKembalian = sprintf('%18s %13.40s','Kembalian. =', angkaDecimal($data['bayar'][ count($data['bayar']) -1 ]['jml_bayar'] - $data['grand_total']));
            $printer -> text("$lineKembalian\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("--------------------------------\n");

            // $printer = new Mike42\Escpos\Printer($connector);
            // $printer -> setJustification(1);
            // $printer -> selectPrintMode(1);
            // $printer -> textRaw("Kalau Tidak Bisa Ambil Hatinya\n");

            // $printer = new Mike42\Escpos\Printer($connector);
            // $printer -> setJustification(1);
            // $printer -> selectPrintMode(1);
            // $printer -> textRaw("Ambil Saja Hikmahnya :D :D :D\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            $printer -> textRaw("Selamat Menikmati\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            $printer -> textRaw("*** TERIMA KASIH ***\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(1);
            // $printer -> textRaw($data['bayar'][ count($data['bayar']) -1 ]['tgl_trans']."\n");

            $conf = new \Model\Storage\Conf();
            $now = $conf->getDate();

            $printer -> textRaw($now['waktu']."\n");

            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }

    public function printCheckList()
    {
        // $params = json_decode($this->input->post('params'), 1);
        $params = $this->input->post('params');

        try {
            $data = $this->getDataCheckList( $params );

            // Enter the share name for your USB printer here
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $printer -> setJustification(1);
            $printer -> selectPrintMode(32);
            $printer -> setTextSize(2, 1);
            $printer -> text("CHECK LIST ORDER\n");
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(0);
            $printer -> selectPrintMode(1);
            $lineNoTransaksi = sprintf('%-13s %1.05s %-15s','No. Transaksi',':', $data['kode_faktur']);
            $printer -> text("$lineNoTransaksi\n");
            $lineKasir = sprintf('%-13s %1.05s %-15s','Pelanggan',':', $data['member']);
            $printer -> text("$lineKasir\n");

            $conf = new \Model\Storage\Conf();
            $now = $conf->getDate();

            $lineTanggal = sprintf('%-13s %1.05s %-15s','Tanggal',':', $now['waktu']);
            $printer -> text("$lineTanggal\n");

            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> textRaw("\n================================\n\n");
            // $printer -> textRaw("--------------------------------\n");
            foreach ($data['jenis_pesanan'] as $k_jp => $v_jp) {
                // $printer = new Mike42\Escpos\Printer($connector);
                $printer -> setJustification(0);
                $printer -> selectPrintMode(0);
                $printer -> textRaw($v_jp['nama']."\n");

                foreach ($v_jp['jual_item'] as $k_ji => $v_ji) {
                    /* NOTE : TABLE
                    $line = sprintf('%-13.40s %3.0f %-3.40s %9.40s %-2.40s %13.40s',$row['item_code'] , $row['item_qty'], $row['kali'], $n1,$row['hasil'], $n2); 
                    */
                    $line = sprintf('%0s %20s',$v_ji['nama'], angkaRibuan($v_ji['jumlah']).' x');
                    $printer -> selectPrintMode(0);
                    $printer -> text("$line\n");

                    if ( !empty($v_ji['detail']) ) {
                        foreach ($v_ji['detail'] as $k_det => $v_det) {
                            $line_detail = sprintf('%2s %13s','', $v_det['menu_nama']);
                            $printer -> selectPrintMode(1);
                            $printer -> text("$line_detail\n");
                        }
                    }
                }
            }
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> setJustification(1);
            $printer -> selectPrintMode(8);
            $printer -> text("--------------------------------\n");

            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }

    public function modalListBayar()
    {
        try {
            $today = date('Y-m-d');
            // $today = '2022-08-01';

            $start_date = $today.' 00:00:00';
            $end_date = $today.' 23:59:59';

            $m_jual = new \Model\Storage\Jual_model();
            $d_jual = $m_jual->whereBetween('tgl_trans', [$start_date, $end_date])->where('kasir', $this->userid)->where('mstatus', 1)->with(['jual_item', 'jual_diskon', 'bayar'])->get();

            $data_bayar = ($d_jual->count() > 0) ? $this->getDataBayar($d_jual) : null;
            $data_belum_bayar = ($d_jual->count() > 0) ? $this->getDataBelumBayar($d_jual) : null;

            $content['data'] = array(
                'data_bayar' => $data_bayar,
                'data_belum_bayar' => $data_belum_bayar
            );

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
                $data[ $v_data['kode_faktur'] ] = array(
                    'kode_faktur' => $v_data['kode_faktur'],
                    'pelanggan' => $v_data['member'],
                    'total' => $v_data['grand_total']
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
                $data[ $v_data['kode_faktur'] ] = array(
                    'kode_faktur' => $v_data['kode_faktur'],
                    'pelanggan' => $v_data['member'],
                    'total' => $v_data['grand_total']
                );
            }
        }

        return $data;
    }

    public function modalHelp()
    {
        $html = $this->load->view($this->pathView . 'modal_help', null, TRUE);

        echo $html;
    }

    public function printTes()
    {
        try {
            $connector = new Mike42\Escpos\PrintConnectors\WindowsPrintConnector('kasir');

            /* Print a receipt */
            $printer = new Mike42\Escpos\Printer($connector);
            $printer -> initialize();

            $printer -> setJustification(1);
            $printer -> selectPrintMode(32);
            $printer -> setTextSize(2, 1);
            $printer -> text("\n\nPRINT TEST\n\n");

            $printer -> cut();
            $printer -> close();

            $this->result['status'] = 1;
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }

    public function cekPinOtorisasi()
    {
        $pin = $this->input->post('pin');

        try {
            $m_po = new \Model\Storage\PinOtorisasi_model();
            $d_po = $m_po->where('pin', $pin)->where('status', 1)->first();

            if ( $d_po ) {
                $this->result['status'] = 1;
            } else {
                $this->result['message'] = "PIN Otorisasi yang anda masukkan tidak di temukan.";
            }
        } catch (Exception $e) {
            $this->result['message'] = "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }

        display_json( $this->result );
    }

    public function tes()
    {
        $data = $this->getDataNota( 'JBR1-2207190002' );
    }
}