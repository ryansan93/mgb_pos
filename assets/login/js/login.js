var login = {
    login: function() {
        var baseurl = $('head base').attr('href');
        var username = $('input#username').val().toUpperCase();
        var password = $('input#password').val();
        var pin_branch = $('input#pin_branch').val();
        var jml_uang = $('input#jumlah_uang').val();

        // NOTE : CHECK EMPTY USERNAME AND PASSWORD
        if ( empty(username) || empty(password) ) {
            if (empty(username)) {
                $('input#username').parent().addClass('has-error');
            };
            if (empty(password)) {
                $('input#password').parent().addClass('has-error');
            };
            $('#divinfo').html('<br><div class="alert alert-danger">Username dan Password tidak boleh kosong.</div>');
        } else {
            if ( empty(jml_uang) ) {
                $('input#jumlah_uang').parent().addClass('has-error');
                $('#divinfo').html('<br><div class="alert alert-danger">Jumlah uang tidak boleh kosong.</div>');
            } else {
                $('input').parent().removeClass('has-error');
                // var defaultPage = baseurl + 'home/Home';
                var defaultPage = baseurl + 'transaksi/Penjualan';
                $.ajax({
                    url: baseurl + 'user/Login/checkLogin',
                    data: {
                        username: username,
                        password: password,
                        pin_branch: pin_branch,
                        jml_uang: numeral.unformat($('input#jumlah_uang').val())
                    },
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function() {showLoading();},
                    success: function(data) {
                        if (data.status) {
                            // NOTE : IF USERNAME AND PASSWORD VALID
                            $('#divinfo').html('<br><div class="alert alert-success">' + data.message + ' success' + '</div>');
                            var redirectPage = (window.location.hash != '') ? window.location.hash.substr(1) : defaultPage;
                            window.location.href = redirectPage;
                        } else {
                            // NOTE : IF USERNAME AND PASSWORD NOT VALID
                            $('#divinfo').html('<br><div class="alert alert-danger"> Gagal <br>' + data.message + '</div>');
                        }

                        hideLoading();
                    }
                });
            }
        };

        return false;
    }, // end - login

    enterToTab: function(e, elm) {
        if (e.keyCode == 13) {
            var nextTabIndex = $(elm).data('index') + 1;
            var div_pah = $(elm).closest('div#div-timbang-pah');
            $(div_pah).find('input.timbang'+nextTabIndex).focus();
        };
    }, // end - enterToTab
};