var kode_member = null;
var member = null;
var jenis_pesanan = null;
var nama_jenis_pesanan = null;
var detail_pesanan = null;
var jenis_bayar = 'tunai';
var gTotal = 0;
var gKurangBayar = 0;
var gBayar = 0;
var dataPenjualan = null;
var kodeKartu = null;
var namaKartu = null;
var noBukti = null;
var kodeFaktur = null;

var jual = {
	start_up: function () {
        jual.modalJenisPesanan();
	}, // end - start_up

    modalJenisPesanan: function () {
        $('.modal').modal('hide');

        $.get('transaksi/Penjualan/modalJenisPesanan',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '60%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $(this).find('.button:not(.btn-exit)').click(function() {
                    jenis_pesanan = $(this).data('kode');
                    nama_jenis_pesanan = $(this).find('span b').text();

                    if ( empty(member) ) {
                        jual.modalPilihMember();
                    } else {
                        $('.list_menu').find('.jenis_pesanan').attr('data-kode', jenis_pesanan);
                        $('.list_menu').find('.jenis_pesanan').text(nama_jenis_pesanan);

                        $('div.kategori').find('ul.kategori li[data-aktif=1]').click();

                        $('.modal').modal('hide');
                    }
                });

                $(this).find('.btn-exit').click(function() { $('.modal').modal('hide'); });
            });
        },'html');
    }, // end - modalJenisPesanan

    modalPilihMember: function () {
        $('.modal').modal('hide');

        $.get('transaksi/Penjualan/modalPilihMember',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '60%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $(this).find('.btn-member').click(function() { jual.modalMember(); });
                $(this).find('.btn-non-member').click(function() { jual.modalNonMember(); });
                $(this).find('.btn-add-member').click(function() { jual.addMember(); });
                $(this).find('.btn-exit').click(function() { $('.modal').modal('hide'); });
            });
        },'html');
    }, // end - modalPilihMember

    modalNonMember: function () {
        $('.modal').modal('hide');

        $.get('transaksi/Penjualan/modalNonMember',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '30%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                var modal_dialog = $(this).find('.modal-dialog');

                $(modal_dialog).find('input').focus();

                $(this).find('.btn-cancel').click(function() { jual.modalPilihMember(); });
                $(this).find('.btn-ok').click(function() { 
                    kode_member = null;
                    member = $(modal_dialog).find('input').val().toUpperCase();

                    $('.member').attr('data-kode', kode_member);
                    $('.member').text(member);
                    $('.list_menu').find('.jenis_pesanan').attr('data-kode', jenis_pesanan);
                    $('.list_menu').find('.jenis_pesanan').text(nama_jenis_pesanan);

                    $.map( $('div.kategori').find('ul.kategori li'), function(li) {
                        var kategori = $(li).text();

                        if ( kategori == 'PAKET' ) {
                            $(li).click();
                        }
                    });

                    $('.list_diskon').find('div.diskon[data-member=1]').remove();
                    jual.hitDiskon();

                    $('.modal').modal('hide');
                });
            });
        },'html');
    }, // end - modalNonMember

    pilihMember: function(elm) {
        var tbody = $(elm).closest('tbody');

        $(tbody).find('tr').attr('data-aktif', 0);
        $(tbody).find('tr').removeAttr('style');

        $(elm).css({'background-color': '#ff8f26'});
        $(elm).attr('data-aktif', 1);
    }, // end - pilihMember

    modalMember: function () {
        $('.modal').modal('hide');

        $.get('transaksi/Penjualan/modalMember',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).css({'height': '100%'});
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});
                $(this).find('.modal-dialog').css({'height': '100%'});
                $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});
                $(this).find('.modal-content').css({'height': '90%'});
                $(this).find('.modal-body').css({'height': '100%'});
                $(this).find('.bootbox-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body .row').css({'height': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                var modal_dialog = $(this).find('.modal-dialog');
                var div = $(modal_dialog).find('.list_member');

                $(this).find('.btn-cancel').click(function() { jual.modalPilihMember(); });
                $(this).find('.btn-ok').click(function() { 
                    kode_member = $(modal_dialog).find('tr[data-aktif=1] td.kode').text().toUpperCase();
                    member = $(modal_dialog).find('tr[data-aktif=1] td.nama').text().toUpperCase();

                    $('.member').attr('data-kode', kode_member);
                    $('.member').text(member+' (MEMBER)');
                    $('.list_menu').find('.jenis_pesanan').attr('data-kode', jenis_pesanan);
                    $('.list_menu').find('.jenis_pesanan').text(nama_jenis_pesanan);

                    $.map( $('div.kategori').find('ul.kategori li'), function(li) {
                        var kategori = $(li).text();

                        if ( kategori == 'PAKET' ) {
                            $(li).click();
                        }
                    });

                    $('.list_diskon').find('div.diskon[data-member=0]').remove();
                    jual.hitDiskon();

                    $('.modal').modal('hide');
                });
            });
        },'html');
    }, // end - modalMember

    addMember: function () {
        $('.modal').modal('hide');

        $.get('transaksi/Penjualan/addMember',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '40%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $(this).find('.btn-danger').on('click', function() {
                    jual.modalPilihMember();
                });

                $(this).find('.btn-primary').on('click', function() {
                    jual.saveMember($(this));
                });
            });
        },'html');
    }, // end - addMember

    saveMember: function (elm) {
        var modal = $(elm).closest('.modal');

        var err = 0;

        $.map( $(modal).find('[data-required=1]'), function(ipt) {
            if ( empty( $(ipt).val() ) ) {
                $(ipt).parent().addClass('has-error');
                err++;
            } else {
                $(ipt).parent().removeClass('has-error');
            }
        });

        if ( err == 0 ) {
            bootbox.confirm('Apakah anda yakin ingin menyimpan data member ?', function( result ) {
                if ( result ) {
                    var params = {
                        'nama': $(modal).find('.nama').val(),
                        'no_telp': $(modal).find('.no_telp').val(),
                        'alamat': $(modal).find('.alamat').val()
                    };

                    $.ajax({
                        url: 'transaksi/Penjualan/saveMember',
                        data: {
                            'params': params
                        },
                        type: 'POST',
                        dataType: 'JSON',
                        beforeSend: function() { showLoading(); },
                        success: function(data) {
                            hideLoading();

                            if ( data.status == 1 ) {
                                bootbox.alert(data.message, function() {
                                    kode_member = data.content.kode_member;
                                    member = data.content.nama;

                                    $('.member').attr('data-kode', kode_member);
                                    $('.member').text(member+' (MEMBER)');
                                    $('.jenis_pesanan').attr('data-kode', jenis_pesanan);
                                    $('.jenis_pesanan').text(nama_jenis_pesanan);

                                    $.map( $('div.kategori').find('ul.kategori li'), function(li) {
                                        var kategori = $(li).text();

                                        if ( kategori == 'PAKET' ) {
                                            $(li).click();
                                        }
                                    });

                                    $('.list_diskon').find('div.diskon[data-member=0]').remove();
                                    jual.hitDiskon();

                                    $('.modal').modal('hide');
                                });
                            } else {
                                bootbox.alert(data.message);
                            }
                        }
                    });
                }
            });
        }
    }, // end - saveMember

	getMenu: function (elm) {
        var id_kategori = $(elm).attr('data-id');

		$.ajax({
            url: 'transaksi/Penjualan/getMenu',
            data: {
                'id_kategori': id_kategori,
            	'jenis_pesanan': jenis_pesanan
            },
            type: 'GET',
            dataType: 'html',
            beforeSend: function() {},
            success: function(html) {
                $('div.detail_menu').html( html );

                $('li').attr('data-aktif', 0);
                $(elm).attr('data-aktif', 1);
            }
        });
	}, // end - getMenu

    cekPaket: function(elm) {
        var jml_paket = $(elm).data('jmlpaket');

        if ( jml_paket > 0 ) {
            jual.modalPaketMenu($(elm));
        } else {
            jual.pilihMenu($(elm));
        }
    }, // end - cekPaket

    modalPaketMenu: function (elm) {
        $('.modal').modal('hide');

        $.get('transaksi/Penjualan/modalPaketMenu',{
            'menu_kode': $(elm).data('kode')
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).css({'height': '100%'});
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});
                $(this).find('.modal-dialog').css({'height': '100%'});
                $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});
                $(this).find('.modal-content').css({'height': '90%'});
                $(this).find('.modal-body').css({'height': '100%'});
                $(this).find('.bootbox-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body .row').css({'height': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                var modal_body = $(this).find('.modal-body');
                $(modal_body).find('.button:not(.btn-remove, .btn-add, .btn-cancel, .btn-ok)').click(function() {
                    var kode_paket_menu = $(this).attr('data-kode');

                    $(modal_body).find('div.detail').addClass('hide');
                    $(modal_body).find('div.detail[data-kode='+kode_paket_menu+']').removeClass('hide');
                });
                $(modal_body).find('.pilih').click(function() {
                    var div_detail = $(this).closest('div.detail');
                    var menu_det = $(this).closest('div.menu_det');
                    var max_pilih = $(div_detail).data('maxpilih');

                    var data_pilih = $(this).attr('data-pilih');
                    if ( data_pilih == 0 ) {
                        var jml_pilih = $(div_detail).find('td.pilih[data-pilih=1]').length;
                        if ( jml_pilih < max_pilih ) {
                            $(this).attr('data-pilih', 1);
                            $(this).find('i').removeClass('hide');

                            var jumlah = numeral.unformat($(menu_det).find('.jumlah span').text());
                            $(menu_det).find('.jumlah span').text( numeral.formatInt(1) );

                            $(menu_det).find('.btn-remove').removeClass('disable');
                            $(menu_det).find('.btn-add').removeClass('disable');
                            $(menu_det).find('.btn-remove').addClass('button');
                            $(menu_det).find('.btn-add').addClass('button');

                            $(menu_det).find('.btn-remove').click(function() {
                                var td = $(this).closest('td');
                                var div_jumlah = $(td).find('.jumlah');
                                var min_jumlah = $(div_jumlah).data('min');

                                var jumlah = numeral.unformat($(div_jumlah).find('span').text());

                                if ( jumlah > min_jumlah ) {
                                    jumlah -= 1;

                                    $(div_jumlah).find('span').text( numeral.formatInt(jumlah) );
                                }
                            });
                            $(menu_det).find('.btn-add').click(function() {
                                var td = $(this).closest('td');
                                var div_jumlah = $(td).find('.jumlah');
                                var max_jumlah = $(div_jumlah).data('max');

                                var jumlah = numeral.unformat($(div_jumlah).find('span').text());

                                if ( jumlah < max_jumlah ) {
                                    jumlah += 1;

                                    $(div_jumlah).find('span').text( numeral.formatInt(jumlah) );
                                }
                            });
                        }
                    } else {
                        $(this).attr('data-pilih', 0);
                        $(this).find('i').addClass('hide');

                        $(menu_det).find('.jumlah span').text( numeral.formatInt(0) );

                        $(menu_det).find('.btn-remove').addClass('disable');
                        $(menu_det).find('.btn-add').addClass('disable');
                        $(menu_det).find('.btn-remove').removeClass('button');
                        $(menu_det).find('.btn-add').removeClass('button');

                        $(menu_det).find('.btn-remove').unbind('click');
                        $(menu_det).find('.btn-add').unbind('click');
                    }
                });

                $(modal_body).find('.btn-cancel').click(function() { $('.modal').modal('hide'); });
                $(modal_body).find('.btn-ok').click(function() { 
                    var detail = 'kosong';
                    var arr_detail = [];
                    var jml_arr_detail = 0;
                    $.map($(modal_body).find('td.pilih[data-pilih=1]'), function(td) {
                        var tbody = $(td).closest('tbody');

                        var kode = $(td).attr('data-kode');
                        var nama = $(td).find('span b').text();
                        var jumlah = numeral.unformat($(tbody).find('.jumlah span').text());

                        if ( jumlah > 0 ) {
                            detail += (kode+jumlah);

                            arr_detail[kode] = {
                                'kode': kode,
                                'nama': nama,
                                'jumlah': jumlah,
                            };

                            jml_arr_detail++;
                        }
                    });

                    jual.pilihMenu($(elm), detail, arr_detail);

                    $('.modal').modal('hide');
                });
            });
        },'html');
    }, // end - modalPaketMenu

    pilihMenu: function (elm, detail = 'kosong', arr_detail = null) {
        var _div_list_pesanan = $('div.list_pesanan');

        var div_jenis_pesanan = null;
        var _div_jenis_pesanan = '';
        if ( $(_div_list_pesanan).find('div.jenis_pesanan[data-kodejp='+jenis_pesanan+']').length == 0 ) {
            _div_jenis_pesanan += '<div class="col-md-12 cursor-p no-padding jenis_pesanan" style="margin-bottom: 10px;" data-kodejp="'+jenis_pesanan+'">';
            _div_jenis_pesanan += '<div class="col-md-12 cursor-p no-padding">';
            _div_jenis_pesanan += '<span style="font-weight: bold;">'+nama_jenis_pesanan+'</span>';
            _div_jenis_pesanan += '</div>';
            _div_jenis_pesanan += '<div class="col-md-12 cursor-p no-padding pesanan">';
            _div_jenis_pesanan += '</div>';
            _div_jenis_pesanan += '</div>';

            $(_div_list_pesanan).append( _div_jenis_pesanan );
            div_jenis_pesanan = $(_div_list_pesanan).find('div.jenis_pesanan[data-kodejp='+jenis_pesanan+'] div.pesanan');
        } else {
            div_jenis_pesanan = $(_div_list_pesanan).find('div.jenis_pesanan[data-kodejp='+jenis_pesanan+'] div.pesanan');
        }

        var kode = $(elm).data('kode');
        var txt_nama = $(elm).find('div.nama_menu').text();
        var txt_harga = $(elm).find('div.harga_menu').text();
        var harga = numeral.unformat(txt_harga);

        if ( $(div_jenis_pesanan).find('div.menu[data-kode="'+kode+'"][data-detail="'+detail+'"]').length > 0 ) {
            var _harga = numeral.unformat($(div_jenis_pesanan).find('div.menu[data-kode="'+kode+'"][data-detail="'+detail+'"] .hrg').text());
            var _jumlah = numeral.unformat($(div_jenis_pesanan).find('div.menu[data-kode="'+kode+'"][data-detail="'+detail+'"] .jumlah:first').text());

            var _total = _harga * (parseInt(_jumlah) + 1);

            $(div_jenis_pesanan).find('div.menu[data-kode="'+kode+'"][data-detail="'+detail+'"] .jumlah:first').text(numeral.formatInt((parseInt(_jumlah) + 1)));
            $(div_jenis_pesanan).find('div.menu[data-kode="'+kode+'"][data-detail="'+detail+'"] .total').text(numeral.formatInt(_total));

            var _div = $(div_jenis_pesanan).find('div.menu[data-kode="'+kode+'"][data-detail="'+detail+'"]');

            for (var key in arr_detail) {
                if ( $(_div).find('[data-kode='+arr_detail[key]['kode']+']').length > 0 ) {
                    var _jumlah_detail = numeral.unformat($(_div).find('[data-kode='+arr_detail[key]['kode']+'] .jumlah').text());

                    _jumlah_detail += arr_detail[key]['jumlah'];

                    $(_div).find('[data-kode='+arr_detail[key]['kode']+'] .jumlah').text(numeral.formatInt(_jumlah_detail));
                }
            }
        } else {
            var _menu = '';
            _menu += '<div class="col-md-12 cursor-p no-padding menu" style="margin-bottom: 10px;" data-kode="'+kode+'" data-detail="'+detail+'">';
            _menu += '<div class="col-md-11 no-padding menu_utama" onclick="jual.jumlahPesanan(this)">';
            _menu += '<div class="col-md-6 no-padding">';
            _menu += '<span class="nama_menu">'+txt_nama.toUpperCase()+'</span>';
            _menu += '<span> @ <span class="hrg">'+numeral.formatInt(harga)+'</span></span>';
            _menu += '</div>';
            _menu += '<div class="col-md-2 text-right no-padding"><span class="jumlah">1</span></div>';
            _menu += '<div class="col-md-3 text-right no-padding"><span class="total">'+numeral.formatInt(harga)+'</span></div>';
            _menu += '</div>';
            _menu += '<div class="col-md-1 text-center no-padding">';
            _menu += '<span class="col-md-12" style="background-color: #a94442; border-radius: 3px; color: #ffffff; padding-left: 0px; padding-right: 0px;" onclick="jual.hapusMenu(this)">';
            _menu += '<i class="fa fa-times"></i>';
            _menu += '</span>';
            _menu += '</div>';
            if ( !empty(detail) ) {
                for (var key in arr_detail) {
                    _menu += '<div class="col-md-11 detail no-padding" style="font-size:10px;" data-kode="'+arr_detail[key]['kode']+'">';
                    _menu += '<div class="col-md-12 no-padding" style="padding-left: 15px;">';
                    _menu += '<span class="nama_menu">'+arr_detail[key]['nama']+'</span><span> @ <span class="jumlah">'+arr_detail[key]['jumlah']+'</span></span>';
                    _menu += '</div>';
                    _menu += '</div>';
                }
            }
            _menu += '</div>';

            $(div_jenis_pesanan).append( _menu );
        }

        jual.hitSubTotal();
    }, // end - pilihMenu

    hapusMenu: function (elm) {
        var div_jenis_pesanan = $(elm).closest('div.jenis_pesanan');

        $(elm).closest('div.menu').remove();

        if ( $(div_jenis_pesanan).find('div.pesanan div.menu').length == 0 ) {
            $(div_jenis_pesanan).remove();
        }

        jual.hitSubTotal();
    }, // end- hapusMenu

    jumlahPesanan: function (elm) {
        $('.modal').modal('hide');

        var kode = $(elm).closest('.menu').data('kode');
        var detail = $(elm).closest('.menu').data('detail');
        var nama_menu = $(elm).find('.nama_menu').text();
        var jumlah = $(elm).find('.jumlah').text();

        $.get('transaksi/Penjualan/jumlahPesanan',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                var modal_dialog = $(this).find('.modal-dialog');

                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '25%', 'max-width': '100%'});

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $(modal_dialog).find('.nama_menu').text(nama_menu);
                $(modal_dialog).find('.jumlah').text(jumlah);

                $(this).find('.btn-angka').click(function() {
                    var jumlah = numeral.unformat($(modal_dialog).find('.jumlah').text());

                    var btn = $(this);
                    var angka = $(btn).find('b').text();

                    if ( jumlah.toString().length == 1 ) {
                        if ( jumlah == 0 ) {
                            $(modal_dialog).find('.jumlah').text(angka);
                        } else {
                            $(modal_dialog).find('.jumlah').text(numeral.formatInt(jumlah.toString()+angka));
                        }
                    } else {
                        $(modal_dialog).find('.jumlah').text(numeral.formatInt(jumlah.toString()+angka));
                    }
                });

                $(this).find('.btn-erase').click(function() {
                    var jumlah = numeral.unformat($(modal_dialog).find('.jumlah').text());

                    var length_jumlah = jumlah.toString().length;

                    var _new_jumlah = jumlah.toString().substring(0, (length_jumlah-1));

                    $(modal_dialog).find('.jumlah').text(numeral.formatInt(_new_jumlah));
                });

                $(this).find('.btn-cancel').click(function() {
                    $('.modal').modal('hide');
                });

                $(this).find('.btn-ok').click(function() {
                    var div = $('.list_pesanan').find('.menu[data-kode='+kode+'][data-detail='+detail+']');

                    var _harga = numeral.unformat($(div).find('.hrg').text());
                    var _jumlah = numeral.unformat($(modal_dialog).find('.jumlah').text());

                    var _total = _harga * _jumlah;

                    $(div).find('.jumlah').text(numeral.formatInt(_jumlah));
                    $(div).find('.total').text(numeral.formatInt(_total));

                    jual.hitSubTotal();

                    $('.modal').modal('hide');
                });
            });
        },'html');
    }, // end - jumlahPesanan

    modalDiskon: function () {
        $('.modal').modal('hide');

        $.get('transaksi/Penjualan/modalDiskon',{
            'kode_member': $('.member').attr('data-kode')
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});
                $(this).find('.modal-dialog').css({'height': '100%'});
                $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});
                $(this).find('.modal-content').css({'height': '90%'});
                $(this).find('.modal-body').css({'height': '100%'});
                $(this).find('.bootbox-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body .row').css({'height': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                var modal_dialog = $(this).find('.modal-dialog');

                $(this).find('.btn-cancel').click(function() { $('.modal').modal('hide'); });
                $(this).find('.btn-ok').click(function() { 
                    var tr_header = $(modal_dialog).find('tr.header[data-aktif=1]');

                    kode_diskon = $(tr_header).find('td.kode').text().toUpperCase();
                    diskon = $(tr_header).find('td.nama').text().toUpperCase();

                    var persen = numeral.unformat($(tr_header).next('tr.detail').find('td.persen').text());
                    var nilai = numeral.unformat($(tr_header).next('tr.detail').find('td.nilai').text());
                    var non_member = $(tr_header).next('tr.detail').find('td.member').attr('data-nonmember');
                    var member = $(tr_header).next('tr.detail').find('td.member').attr('data-member');
                    var min_beli = numeral.unformat($(tr_header).next('tr.detail').find('td.min_beli').text());
                    var level = numeral.unformat($(tr_header).next('tr.detail').find('td.level').text());

                    var div_list_diskon = $('div.list_diskon');

                    if ( $(div_list_diskon).find('div.diskon[data-kode="'+kode_diskon+'"]').length == 0 ) {
                        var _diskon = '';
                        _diskon += '<div class="col-md-12 cursor-p no-padding diskon" style="margin-bottom: 10px;" data-kode="'+kode_diskon+'" data-persen="'+persen+'" data-nilai="'+nilai+'" data-nonmember="'+non_member+'" data-member="'+member+'" data-minbeli="'+min_beli+'" data-level="'+level+'">';
                        _diskon += '<div class="col-md-11 no-padding">';
                        _diskon += '<div class="col-md-12 no-padding">';
                        _diskon += '<span class="nama_diskon">'+diskon.toUpperCase()+'</span>';
                        _diskon += '</div>';
                        _diskon += '</div>';
                        _diskon += '<div class="col-md-1 text-center no-padding">';
                        _diskon += '<span class="col-md-12" style="background-color: #a94442; border-radius: 3px; color: #ffffff; padding-left: 0px; padding-right: 0px;" onclick="jual.hapusDiskon(this)">';
                        _diskon += '<i class="fa fa-times"></i>';
                        _diskon += '</span>';
                        _diskon += '</div>';
                        _diskon += '</div>';

                        $(div_list_diskon).append( _diskon );
                    }

                    jual.hitDiskon();

                    $('.modal').modal('hide');
                });
            });
        },'html');
    }, // end - modalDiskon

    pilihDiskon: function(elm) {
        var tbody = $(elm).closest('tbody');

        $(tbody).find('tr').attr('data-aktif', 0);
        $(tbody).find('tr').removeAttr('style');

        $(elm).css({'background-color': '#ff8f26'});
        $(elm).attr('data-aktif', 1);
    }, // end - pilihDiskon

    hapusDiskon: function (elm) {
        $(elm).closest('div.diskon').remove();

        jual.hitSubTotal();
    }, // end- hapusDiskon

    hitSubTotal: function() {
        var div = $('.list_pesanan');

        var sub_total = 0;
        $.map( $(div).find('.menu'), function(div_menu) {
            var total = numeral.unformat($(div_menu).find('.total').text());

            sub_total += total;
        });

        var persen_ppn = numeral.unformat($('.persen_ppn').text());
        var total_ppn = (persen_ppn > 0) ? sub_total * (11/100) : 0;

        $('.subtotal').text(numeral.formatDec(sub_total));
        $('.ppn').text(numeral.formatDec(total_ppn));

        jual.hitDiskon();
    }, // end - hitSubTotal

    hitDiskon: function() {
        var div = $('.list_diskon');

        var subtotal = numeral.unformat($('.subtotal').text());

        var diskon = 0;

        var $wrapper = $(div);
        $wrapper.find('div.diskon').sort(function(a, b) {
            return +a.dataset.level - +b.dataset.level;
        })
        .appendTo($wrapper);

        $.map( $(div).find('div.diskon'), function(_div) {
            var min_beli = numeral.unformat($(_div).attr('data-minbeli'));
            var _diskon = 0;

            var hit_diskon = true;
            if ( min_beli > 0 ) {
                if ( subtotal < min_beli ) {
                    hit_diskon = false;
                }
            }

            if ( hit_diskon ) {
                var nilai = numeral.unformat($(_div).attr('data-nilai'));
                if ( nilai > 0 ) {
                    _diskon = numeral.unformat($(_div).attr('data-nilai'));
                    diskon += _diskon;
                }

                var persen = numeral.unformat($(_div).attr('data-persen'));
                if ( persen > 0 ) {
                    _diskon = subtotal * (persen / 100);
                    diskon += _diskon;
                }
            }

            subtotal -= _diskon;
        });

        $('span.diskon').text(numeral.formatDec(diskon));

        jual.hitGrandTotal();
    }, // end - hitDiskon

    hitGrandTotal: function() {
        var subtotal = numeral.unformat($('.subtotal').text());
        var diskon = numeral.unformat($('span.diskon').text());
        var ppn = numeral.unformat($('.ppn').text());

        var grandtotal = (subtotal + ppn) - diskon;

        $('.grandtotal').text(numeral.formatDec(grandtotal));

        gTotal = grandtotal;
        gKurangBayar = grandtotal;
    }, // end - hitGrandTotal

    getPenjualan: function(action) {
        var list_pesanan = $.map( $('.list_pesanan').find('.jenis_pesanan'), function(div_jp) {
            var list_menu = $.map( $(div_jp).find('.menu'), function(div_menu) {
                var div_menu_utama = $(div_menu).find('.menu_utama');

                var detail_menu = $.map( $(div_menu).find('.detail'), function(div_detail) {
                    var kode_menu_detail = $(div_detail).attr('data-kode');
                    var nama_menu_detail = $(div_detail).find('.nama_menu').text();
                    var jumlah_menu_detail = numeral.unformat($(div_detail).find('.jumlah').text());

                    var _detail_menu = {
                        'kode_menu': kode_menu_detail,
                        'nama_menu': nama_menu_detail,
                        'jumlah': jumlah_menu_detail
                    };

                    return _detail_menu;
                });

                var kode_menu = $(div_menu).attr('data-kode');
                var kode_detail_menu = $(div_menu).attr('data-detail');

                var nama_menu = $(div_menu_utama).find('.nama_menu').text();
                var harga = numeral.unformat($(div_menu_utama).find('.hrg').text());
                var jumlah = numeral.unformat($(div_menu_utama).find('.jumlah').text());
                var total = numeral.unformat($(div_menu_utama).find('.total').text());

                var _list_menu = {
                    'kode_menu': kode_menu,
                    'nama_menu': nama_menu,
                    'harga': harga,
                    'jumlah': jumlah,
                    'total': total,
                    'detail_menu': detail_menu
                };

                return _list_menu;

                jml_pesanan++;
            });

            var kode_jp = $(div_jp).attr('data-kodejp');
            var _list_pesanan = {
                'kode_jp': kode_jp,
                'list_menu': list_menu
            };

            return _list_pesanan;
        });

        var list_diskon = $.map( $('.list_diskon').find('.diskon'), function(div_diskon) {
            var kode_diskon = $(div_diskon).attr('data-kode');
            var nama_diskon = $(div_diskon).find('.nama_diskon').text();

            var _data = {
                'kode_diskon': kode_diskon,
                'nama_diskon': nama_diskon
            };

            return _data;
        });

        var sub_total = numeral.unformat($('.subtotal').text());
        var diskon = numeral.unformat($('span.diskon').text());
        var ppn = numeral.unformat($('.ppn').text());
        var grand_total = numeral.unformat($('.grandtotal').text());

        dataPenjualan = {
            'member': member,
            'kode_member': kode_member,
            'sub_total': sub_total,
            'diskon': diskon,
            'ppn': ppn,
            'grand_total': grand_total,
            'list_pesanan': list_pesanan,
            'list_diskon': list_diskon
        };

        action(dataPenjualan);
    }, // end - getPenjualan

    savePenjualan: function(jenis) {
        jual.getPenjualan(function(data) {
            $('.modal').modal('hide');

            $.ajax({
                url: 'transaksi/Penjualan/savePenjualan',
                data: {
                    'params': data
                },
                type: 'POST',
                dataType: 'JSON',
                beforeSend: function() { showLoading(); },
                success: function(data) {
                    hideLoading();
                    if ( data.status == 1 ) {
                        if ( jenis == 'simpan' ) {
                            location.reload();
                        } else {
                            kodeFaktur = data.content.kode_faktur;
                            jual.modalPembayaran();
                        }
                    } else {
                        bootbox.alert(data.message);
                    }
                }
            });
        });
    }, // end - savePenjualan

    modalPilihBayar: function () {
        $('.modal').modal('hide');

        $.get('transaksi/Penjualan/modalPilihBayar',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '60%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                if ( !empty(kode_member) ) {
                    $(this).find('.btn-simpan').click(function() { jual.savePenjualan('simpan'); });
                } else {
                    $(this).find('.btn-simpan').css({'background-color': '#dedede', 'border-color': '#dedede'});
                    $(this).find('.btn-simpan').addClass('disable');
                }
                $(this).find('.btn-lanjut').click(function() { jual.savePenjualan('lanjut'); });
                $(this).find('.btn-batal').click(function() { $('.modal').modal('hide'); });
            });
        },'html');
    }, // end - modalPilihBayar

    modalPembayaran: function () {
        $('.modal').modal('hide');

        $.get('transaksi/Penjualan/modalPembayaran',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '60%', 'max-width': '100%'});
                $(this).find('.modal-dialog').css({'height': '90.5%'});
                $(this).find('.modal-content').css({'width': '100%', 'max-width': '100%'});
                $(this).find('.modal-content').css({'height': '90%'});
                $(this).find('.modal-body').css({'height': '95%'});
                $(this).find('.bootbox-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body').css({'height': '100%'});
                $(this).find('.bootbox-body .modal-body .row').css({'height': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                var modal_body = $(this).find('.modal-body');

                var _gKembali = gBayar - gKurangBayar;
                var gKembali = (_gKembali > 0) ? _gKembali : 0;

                console.log( gKurangBayar );

                $(modal_body).find('.gTotal').text( numeral.formatDec(gTotal) );
                $(modal_body).find('.gKurangBayar').text( numeral.formatDec(gKurangBayar) );
                $(modal_body).find('.gBayar').text( numeral.formatDec(gBayar) );
                $(modal_body).find('.gKembali').text( numeral.formatDec(gKembali) );

                $(this).find('.btn-tunai').click(function() {
                    var jenis = $(this).data('jenis');

                    jenis_bayar = jenis;

                    $(modal_body).find('.btn-tunai, .btn-kartu').attr('data-aktif', 0);
                    $(this).attr('data-aktif', 1);

                    $(modal_body).find('.form_pembayaran').addClass('hide');
                    $(modal_body).find('.'+jenis).removeClass('hide');

                    gBayar = 0;
                });
                $(this).find('.btn-kartu').click(function() {
                    var jenis = $(this).data('jenis');

                    jenis_bayar = jenis;

                    $(modal_body).find('.btn-tunai, .btn-kartu').attr('data-aktif', 0);
                    $(this).attr('data-aktif', 1);

                    $(modal_body).find('.form_pembayaran').addClass('hide');
                    $(modal_body).find('.'+jenis).removeClass('hide');

                    gBayar = gKurangBayar;
                });
                $(this).find('.bayar').click(function() { jual.jumlahBayar(); });
                $(this).find('.jenis_kartu').click(function() { jual.modalJenisKartu(); });
                $(this).find('.no_bukti').click(function() { jual.noBuktiKartu(); });
                $(this).find('.btn-ok-tunai').click(function() { jual.savePembayaran(); });
                $(this).find('.btn-ok-kartu').click(function() { jual.savePembayaran(); });
                $(this).find('.btn-cancel').click(function() { $('.modal').modal('hide'); });
            });
        },'html');
    }, // end - modalPembayaran

    modalJenisKartu: function () {
        $.get('transaksi/Penjualan/modalJenisKartu',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                var btn_close = $(this).find('.close');

                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '60%', 'max-width': '100%'});

                $('input').keyup(function(){
                    $(this).val($(this).val().toUpperCase());
                });

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $(this).find('.btn-jenis-kartu').click(function() {
                    kodeKartu = $(this).attr('data-kode');
                    namaKartu = $(this).find('span b').text();

                    $('.gKartu').text(namaKartu);

                    $(btn_close).click();
                });
            });
        },'html');
    }, // end - modalJenisKartu

    jumlahBayar: function (elm) {
        $('.modal').modal('hide');

        $.get('transaksi/Penjualan/jumlahBayar',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                var modal_dialog = $(this).find('.modal-dialog');

                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '25%', 'max-width': '100%'});

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $(modal_dialog).find('.jumlah').text(0);

                $(this).find('.btn-angka').click(function() {
                    var jumlah = numeral.unformat($(modal_dialog).find('.jumlah').text());

                    var btn = $(this);
                    var angka = $(btn).find('b').text();

                    if ( jumlah.toString().length == 1 ) {
                        if ( jumlah == 0 ) {
                            $(modal_dialog).find('.jumlah').text(angka);
                        } else {
                            $(modal_dialog).find('.jumlah').text(numeral.formatInt(jumlah.toString()+angka));
                        }
                    } else {
                        $(modal_dialog).find('.jumlah').text(numeral.formatInt(jumlah.toString()+angka));
                    }
                });

                $(this).find('.btn-erase').click(function() {
                    var jumlah = numeral.unformat($(modal_dialog).find('.jumlah').text());

                    var length_jumlah = jumlah.toString().length;

                    var _new_jumlah = jumlah.toString().substring(0, (length_jumlah-1));

                    $(modal_dialog).find('.jumlah').text(numeral.formatInt(_new_jumlah));
                });

                $(this).find('.btn-cancel').click(function() {
                    jual.modalPembayaran();
                });

                $(this).find('.btn-ok').click(function() {
                    var _jumlah = numeral.unformat($(modal_dialog).find('.jumlah').text());

                    gBayar = _jumlah;

                    jual.modalPembayaran();
                });
            });
        },'html');
    }, // end - jumlahBayar

    noBuktiKartu: function (elm) {
        $.get('transaksi/Penjualan/noBuktiKartu',{
        },function(data){
            var _options = {
                className : 'large',
                message : data,
                addClass : 'form',
                onEscape: true,
            };
            bootbox.dialog(_options).bind('shown.bs.modal', function(){
                var modal_dialog = $(this).find('.modal-dialog');
                var btn_close = $(this).find('.close');

                $(this).find('.modal-header').css({'padding-top': '0px'});
                $(this).find('.modal-dialog').css({'width': '25%', 'max-width': '100%'});

                $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                    $(this).priceFormat(Config[$(this).data('tipe')]);
                });

                $(modal_dialog).find('.jumlah').text(0);

                $(this).find('.btn-angka').click(function() {
                    var jumlah = numeral.unformat($(modal_dialog).find('.jumlah').text());

                    var btn = $(this);
                    var angka = $(btn).find('b').text();

                    if ( jumlah.toString().length <= 20 ) {
                        if ( jumlah.toString().length == 1 ) {
                            if ( jumlah == 0 ) {
                                $(modal_dialog).find('.jumlah').text(angka);
                            } else {
                                $(modal_dialog).find('.jumlah').text(jumlah.toString()+angka);
                            }
                        } else {
                            $(modal_dialog).find('.jumlah').text(jumlah.toString()+angka);
                        }
                    }
                });

                $(this).find('.btn-erase').click(function() {
                    var jumlah = numeral.unformat($(modal_dialog).find('.jumlah').text());

                    var length_jumlah = jumlah.toString().length;

                    var _new_jumlah = jumlah.toString().substring(0, (length_jumlah-1));

                    $(modal_dialog).find('.jumlah').text(_new_jumlah);
                });

                $(this).find('.btn-cancel').click(function() {
                    jual.modalPembayaran();
                });

                $(this).find('.btn-ok').click(function() {
                    var _jumlah = numeral.unformat($(modal_dialog).find('.jumlah').text());

                    noBukti = _jumlah;

                    $('.gNoBukti').text(noBukti);

                    $(btn_close).click();
                });
            });
        },'html');
    }, // end - noBuktiKartu

    savePembayaran: function() {
        // if ( gBayar > 0 ) {
            var data = {
                'faktur_kode': kodeFaktur,
                'jml_tagihan': gTotal,
                'sisa_tagihan': gKurangBayar,
                'jml_bayar': gBayar,
                'jenis_bayar': jenis_bayar,
                'jenis_kartu_kode': kodeKartu,
                'no_bukti': noBukti
            };

            $.ajax({
                url: 'transaksi/Penjualan/savePembayaran',
                data: {
                    'params': data
                },
                type: 'POST',
                dataType: 'JSON',
                beforeSend: function() { showLoading(); },
                success: function(data) {
                    hideLoading();
                    if ( data.status == 1 ) {
                        // jual.printNota(JSON.stringify(data.content.data));
                        jual.modalPrint(kodeFaktur);
                    } else {
                        bootbox.alert(data.message);
                    }
                }
            });
        // } else {
        //     bootbox.alert('Harap isi jumlah bayar.');
        // }
    }, // end - savePembayaran

    modalPrint: function(kode_faktur) {
        $('.modal').modal('hide');

        bootbox.dialog({
            message: "Transaksi berhasil.",
            buttons: {
                done: {
                    label: '<i class="fa fa-check"></i> SELESAI',
                    className: 'btn-success',
                    callback: function() {
                        location.reload();
                    }
                },
                print: {
                    label: '<i class="fa fa-print"></i> PRINT NOTA',
                    className: 'btn-primary',
                    callback: function() {
                        jual.printNota(kode_faktur);

                        return false;
                    }
                },
                print_check_list: {
                    label: '<i class="fa fa-print"></i> PRINT CHECK LIST',
                    className: 'btn-primary',
                    callback: function() {
                        jual.printCheckList(kode_faktur);

                        return false;
                    }
                }
            }
        });
    }, // end - modalPrint

    printNota: function(kode_faktur) {
        $.ajax({
            url: 'transaksi/Penjualan/printNota',
            data: {
                'params': kode_faktur
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {},
            success: function(data) {
                if ( data.status != 1 ) {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end - printNota

    printCheckList: function(kode_faktur) {
        $.ajax({
            url: 'transaksi/Penjualan/printCheckList',
            data: {
                'params': kode_faktur
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {},
            success: function(data) {
                if ( data.status != 1 ) {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end - printNota

    modalListBayar: function () {
        $('.modal').modal('hide');

        $.ajax({
            url: 'transaksi/Penjualan/modalListBayar',
            data: {},
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading(); },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    var _options = {
                        className : 'large',
                        message : data.html,
                        addClass : 'form',
                        onEscape: true,
                    };
                    bootbox.dialog(_options).bind('shown.bs.modal', function(){
                        $(this).find('.modal-header').css({'padding-top': '0px'});
                        $(this).find('.modal-dialog').css({'width': '75%', 'max-width': '100%'});

                        $('input').keyup(function(){
                            $(this).val($(this).val().toUpperCase());
                        });

                        $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                            $(this).priceFormat(Config[$(this).data('tipe')]);
                        });

                        var modal_body = $(this).find('.modal-body');
                        $.map( $(modal_body).find('li.nav-item'), function(li) {
                            $(li).click(function() {
                                var id = $(li).find('a').attr('href');

                                $(modal_body).find('.tab-pane').removeClass('show');
                                $(modal_body).find('.tab-pane').removeClass('active');

                                $(modal_body).find(id).addClass('show');
                                $(modal_body).find(id).addClass('active');
                            });
                        });

                        $(this).find('tr.belum_bayar td:not(.btn-delete)').click(function() {
                            var tr = $(this).closest('tr.belum_bayar');
                            kodeFaktur = $(tr).find('td.kode_faktur').html();
                            gTotal = numeral.unformat($(tr).find('td.total').html());
                            gKurangBayar = gTotal;

                            jual.modalPembayaran();
                        });

                        $(this).find('tr.belum_bayar .btn').click(function() {
                            var tr = $(this).closest('tr.belum_bayar');
                            var kode_faktur = $(tr).find('td.kode_faktur').html();

                            // jual.deletePenjualan( kode_faktur ); 
                            jual.verifikasiPinOtorisasi( kode_faktur ); 
                        });

                        $(this).find('tr.bayar td:not(.btn-delete)').click(function() {
                            var tr = $(this).closest('tr.bayar');
                            var kode_faktur = $(tr).find('td.kode_faktur').html();
                            jual.modalDetailFaktur( kode_faktur );
                        });

                        $(this).find('tr.bayar .btn').click(function() {
                            var tr = $(this).closest('tr.bayar');
                            var kode_faktur = $(tr).find('td.kode_faktur').html();
                            // jual.deletePenjualan( kode_faktur ); 
                            jual.verifikasiPinOtorisasi( kode_faktur ); 
                        });

                        $(this).find('.btn_print_closing_shift').click(function() {
                            jual.printClosingShift(); 
                        });
                    });
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end - modalListBayar

    modalDetailFaktur: function (kode_faktur) {
        $('.modal').modal('hide');

        $.ajax({
            url: 'transaksi/Penjualan/modalDetailFaktur',
            data: {
                'kode_faktur': kode_faktur
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { showLoading(); },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    var _options = {
                        className : 'large',
                        message : data.html,
                        addClass : 'form',
                        onEscape: true,
                    };
                    bootbox.dialog(_options).bind('shown.bs.modal', function(){
                        $(this).find('.modal-header').css({'padding-top': '0px'});
                        $(this).find('.modal-dialog').css({'width': '70%', 'max-width': '100%'});

                        $('input').keyup(function(){
                            $(this).val($(this).val().toUpperCase());
                        });

                        $('[data-tipe=integer],[data-tipe=angka],[data-tipe=decimal]').each(function(){
                            $(this).priceFormat(Config[$(this).data('tipe')]);
                        });

                        $(this).find('.btn-cancel').click(function() { 
                            jual.modalListBayar();
                            // $(this).closest('.modal').modal('hide'); 
                        });
                        $(this).find('.btn-ok').click(function() { jual.modalPrint( kode_faktur ); });

                        $(this).find('.btn-del-bayar').click(function() {
                            var tr = $(this).closest('tr');
                            var id = $(tr).attr('data-id');

                            jual.verifikasiPinOtorisasi(kode_faktur, id);
                        });

                        // $(this).find('tr.bayar td:not(.btn-delete)').click(function() {
                        //     var tr = $(this).closest('tr.bayar');
                        //     var kode_faktur = $(tr).find('td.kode_faktur').html();
                        //     jual.modalPrint( kode_faktur );
                        // });

                        // $(this).find('tr.bayar .btn').click(function() {
                        //     var tr = $(this).closest('tr.bayar');
                        //     var kode_faktur = $(tr).find('td.kode_faktur').html();
                        //     // jual.deletePenjualan( kode_faktur ); 
                        //     jual.verifikasiPinOtorisasi( kode_faktur ); 
                        // });
                    });
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end - modalDetailFaktur

    verifikasiPinOtorisasi: function(kode_faktur, id_pembayaran = null) {
        bootbox.dialog({
            message: '<p>Masukkan PIN Otorisasi untuk menghapus data.</p><p><input type="password" class="form-control text-center pin" data-tipe="angka" placeholder="PIN" /></p>',
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Batal',
                    className: 'btn-danger',
                    callback: function(){}
                },
                ok: {
                    label: '<i class="fa fa-check"></i> Lanjut',
                    className: 'btn-primary',
                    callback: function(){
                        var pin = $('.pin').val();

                        $.ajax({
                            url: 'transaksi/Penjualan/cekPinOtorisasi',
                            data: {
                                'pin': pin
                            },
                            type: 'POST',
                            dataType: 'JSON',
                            beforeSend: function() { showLoading(); },
                            success: function(data) {
                                // hideLoading();
                                if ( data.status == 1 ) {
                                    if ( empty(id_pembayaran) ) {
                                        jual.deletePenjualan(kode_faktur);
                                    } else {
                                        jual.deletePembayaran(kode_faktur, id_pembayaran);
                                    }
                                } else {
                                    bootbox.alert(data.message, function() {
                                        jual.verifikasiPinOtorisasi(kode_faktur);
                                    });
                                }
                            }
                        });
                    }
                }
            }
        });
    }, // end - verifikasiPinOtorisasi

    deletePenjualan: function(kode_faktur) {
        // bootbox.confirm('Apakah anda yakin ingin meng-hapus data penjualan <b>'+kode_faktur+'</b> ?', function(result) {
        //     if ( result ) {
        $.ajax({
            url: 'transaksi/Penjualan/deletePenjualan',
            data: {
                'params': kode_faktur
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { 
                // showLoading();
            },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    bootbox.alert(data.message, function() {
                        $('.modal').modal('hide');

                        jual.modalListBayar();
                    });
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
        //     }
        // });
    }, // end - deletePenjualan

    deletePembayaran: function(kode_faktur, id_pembayaran) {
        // bootbox.confirm('Apakah anda yakin ingin meng-hapus data penjualan <b>'+kode_faktur+'</b> ?', function(result) {
        //     if ( result ) {
        $.ajax({
            url: 'transaksi/Penjualan/deletePembayaran',
            data: {
                'params': id_pembayaran
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() { 
                // showLoading();
            },
            success: function(data) {
                hideLoading();
                if ( data.status == 1 ) {
                    bootbox.alert(data.message, function() {
                        $('.modal').modal('hide');

                        jual.modalDetailFaktur( kode_faktur );
                    });
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
        //     }
        // });
    }, // end - deletePembayaran

    openModalPembayaran: function(elm) {
        var tr = $(elm);
        kodeFaktur = $(tr).find('td.kode_faktur').text();
        gTotal = numeral.unformat($(tr).find('td.total').text());
        gKurangBayar = gTotal;

        jual.modalPembayaran();
    }, // end - openModalPembayaran

    filterMenu: function() {
        clearTimeout(timeOutFilter);

        timeOutFilter = setTimeout(jual.funcFilterMenu(), 500);
    }, // end - filterMenu

    funcFilterMenu: function() {
        var div_detail_menu = $('.detail_menu');
        
        var val = $('.filter_menu').val().toUpperCase();

        $(div_detail_menu).find('div.menu').removeClass('hide');
        $.map( $(div_detail_menu).find('div.menu'), function(div_menu) {
            var nama_menu = $(div_menu).find('.nama_menu').text();
            if (nama_menu.trim().toUpperCase().indexOf(val) > -1) {
                $(div_menu).removeClass('hide'); 
            } else {
                $(div_menu).addClass('hide');
            }
        });
    }, // end - funcFilterMenu

    printClosingShift: function() {
        $.ajax({
            url: 'transaksi/Penjualan/printClosingShift',
            data: {},
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {},
            success: function(data) {
                if ( data.status != 1 ) {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end - printNota
};

jual.start_up();
var timeOutFilter = setTimeout(jual.funcFilterMenu(), 500);