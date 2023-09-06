var ls = {
	startUp: function () {
		ls.settingUp();
	}, // end - startUp

	settingUp: function () {
		$("#Tanggal").datetimepicker({
            locale: 'id',
            format: 'DD MMM Y'
        });
	}, // end - settingUp

	getLists: function () {
		var err = 0;

		if ( empty($('#Tanggal').find('input').val()) ) {
			$('#Tanggal').parent().addClass('has-error');
			err++;
		} else {
			$('#Tanggal').parent().removeClass('has-error');
		}

		if ( err > 0 ) {
			bootbox.alert('Harap lengkapi data terlebih dahulu.');
		} else {
			var params = {
				'tanggal': dateSQL( $('#Tanggal').data('DateTimePicker').date() )
			};

			$.ajax({
	            url: 'transaksi/ListShift/getLists',
	            data: {
	                'params': params
	            },
	            type: 'GET',
	            dataType: 'HTML',
	            beforeSend: function() { showLoading(); },
	            success: function(html) {
	                hideLoading();
	                
	                $('table tbody').html( html );

                    $.map( $('table tbody tr td'), function (td) {
                        if ( $(td).find('button').length == 0 ) {
                            $(td).on('click', function () {
                                ls.modalListBayar( $(this) );
                            });
                        }
                    });
	            }
	        });
		}
	}, // end - getLists

	modalListBayar: function (elm) {
        $('.modal').modal('hide');

        var tr = $(elm).closest('tr');

        var params = {
        	'id': $(tr).attr('data-id')
        };

        $.ajax({
            url: 'transaksi/ListShift/modalListBayar',
            data: {
            	'params': params
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
                    });
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }, // end - modalListBayar

    printClosingShift: function(elm) {
    	var id = $(elm).attr('data-id');

    	var params = {
    		'id': id
    	};

        $.ajax({
            url: 'transaksi/ListShift/printClosingShift',
            data: {
            	'params': params
            },
            type: 'POST',
            dataType: 'JSON',
            beforeSend: function() {},
            success: function(data) {
                if ( data.status == 1 ) {
                    $('.modal').modal('hide')
                } else {
                    bootbox.alert(data.message);
                } 
            }
        });
    }, // end - printNota

    delete: function(elm) {
        var tr = $(elm).closest('tr');

        bootbox.alert('Apakah anda yakin ingin menghapus data end shift ?', function(result) {
            if ( result ) {
                var params = {
                    'id': $(tr).attr('data-id')
                };

                $.ajax({
                    url: 'transaksi/ListShift/delete',
                    data: {
                        'params': params
                    },
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function() { showLoading(); },
                    success: function(data) {
                        hideLoading();

                        if ( data.status == 1 ) {
                            ls.getLists();
                        } else {
                            bootbox.alert(data.message);
                        } 
                    }
                });
            }
        });
    }, // end - delete
};

ls.startUp();