function siapkanFilterRW() {
    var top = bot = '';
    $('#rw').empty();
    $.ajax({
            url: '/beranda/getRW',
            data: {
                init: $(location).attr("href")
            },
            method: 'post',
            dataType: 'json'
        })
        .done(function(data) {
            if (data.length > 0) {
                top = `<option selected value=''>Semua RW</option>`;
                $.each(data, function(i, data) {
                    bot += `<option value="` + data['rw'] + `">RW ` + data['rw'] + `</option>`
                })
                $('#rw').prop('hidden', false);
            } else {
                $('#rw').prop('hidden', true);
            }
            $('#rw').prepend(top + bot);
            periksaChecklist();
        })
}

function siapkanFilterRT(rw = '') {
    var top = bot = '';
    $.ajax({
            url: '/beranda/getRT',
            data: {
                rw: rw
            },
            method: 'post',
            dataType: 'json'
        })
        .done(function(data) {
            top = bot = '';
            $('#rt').empty();
            if (rw !== null && rw !== '') {

                if (data.length > 0) {
                    top = `<option selected value=''>Semua RT</option>`;
                    $.each(data, function(i, data) {
                        bot += `<option value="` + data['rt'] + `">RT ` + data['rt'] + `</option>`
                    })
                    $('#rt').prop('hidden', false);
                    $('#filterRT').removeClass('d-none');
                } else {
                    $('#filterRT').addClass('d-none');
                    $('#rt').prop('hidden', true);
                }
                $('#rt').prepend(top + bot);
            } else {
                $('#filterRT').addClass('d-none');
                $('#rt').prop('hidden', true);
            }
            periksaChecklist();
        })
}

function siapkanHalaman(q = '', rw = null, rt = null) {
    var top = '';
    $.ajax({
            url: '/beranda/getHalaman',
            data: {
                rw: rw,
                rt: rt,
                q: q
            },
            method: 'post',
            dataType: 'json'
        })
        .done(function(data) {
            top = bot = '';
            $('#halaman').empty();
            bot += `<option selected value="1">1</option>`
            for (let i = 2; i <= Math.ceil(data / 25); i++) {
                bot += `<option value="` + i + `"> ` + i + `</option>`
            }
            $('#halaman').prepend(top + bot);
            periksaChecklist();
        })
}

function tampilkanHeaderTabel() {

}

function tampilkanTabelPenduduk(q = '', rw = null, rt = null, halaman = 1, init = 0) {
    if ($('#loading').is(':empty')) {

        $('#loading').prepend(
            `<div class="spinner-border text-primary mt-3" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>`
        );
    }
    var top = mid = bot = '';
    $.ajax({
            url: '/beranda/getDataTabelPenduduk',
            data: {
                q: q,
                rw: rw,
                rt: rt,
                halaman: halaman
            },
            method: 'post',
            dataType: 'json'
        })
        .done(function(data) {
            if (data.length > 0) {
                top = `<table class='table table-striped table-bordered table-hover overflow-auto'>
                        <thead>
                            <tr>
                                <th class='text-center' scope='col' style='min-width:2.68%; max-width:2.68%; width:2.68%;'><input class='form-check-input pilih-semua' type='checkbox'></th>
                                <th class='text-center' scope='col' style='min-width:15%; max-width:15%; width:15%;'>NIK</th>
                                <th class='text-center' scope='col' style='min-width:21%; max-width:21%; width:21%;'>Nama</th>
                                <th class='text-center' scope='col' style='min-width:51.82%; max-width:51.82%; width:51.82%;'>Alamat</th>
                                <th class='text-center' scope='col' style='min-width:9.5%; max-width:9.5%; width:9.5%;'>Opsi</th>
                            </tr>
                        </thead>
                        <tbody>`;
                $.each(data, function(i, data) {
                    mid += `<tr class='align-middle'>
                            <th class='text-center' scope='row'><input class='form-check-input tabel-check' type='checkbox' value='` + data.hashId + `' name='penduduk[]'></th>
                            <td>` + data.nik + `</td>
                            <td>` + data.nama + `</td>
                            <td>` + data.alamatRumah + `</td>
                            <td class='text-center'>
                                <a class=' link-primary text-decoration-none fw-bold' href='detailpenduduk/index/` + data.hashId + `'>Detail</a>
                                <a class=' link-dark text-decoration-none fw-bold' href='ubahpenduduk/index/` + data.hashId + `'>Ubah</a>
                            </td>
                            </tr >`
                })
                bot = `</tbody >
                    </table >`;
                $('#message').addClass('hidden')
                $('#message').empty();
                if (init == 0) {
                    $('#tabel-container').append(top + mid + bot);
                } else {
                    $('tbody').html(mid);
                }
                $('#tabel-container').prop('hidden', false)
            } else {
                bot = `<p class='display-6 mt-5'>Tidak ada data</p>`
                $('#tabel-container').prop('hidden', true)
                $('tbody').html('');
                $('#message').empty();
                $('#message').append(bot);
                $('#message').removeClass('hidden')
            }
            $('#loading').empty()
            periksaChecklist();
        })
}

function periksaChecklist() {
    $('input[type=checkbox]').prop('checked', false);
    $('#tombolHapus').attr('disabled', true);
    if ($('input[type=checkbox]').length == 1) {
        $('.pilih-semua').attr('disabled', true);
    } else {
        $('.pilih-semua').removeAttr('disabled');
    }

    $('.pilih-semua').change('click', function() {
        if ($(this).is(':checked')) {
            $('.tabel-check').prop('checked', true);
        } else {
            $('.tabel-check').prop('checked', false);
        }

    })
    $('.tabel-check').change('click', function() {
        if ($('.tabel-check:checked').length < $('.tabel-check').length) {
            $('.pilih-semua').prop('checked', false)
        } else {
            $('.pilih-semua').prop('checked', true)
        }

    })
    $('input[type=checkbox]').change('click', function() {
        if ($('input[type=checkbox]:checked').length > 0) {
            $('#tombolHapus').removeAttr('disabled');
        } else {
            $('#tombolHapus').attr('disabled', true);
        }
    })


}

$(function() {
    siapkanHalaman();
    tampilkanTabelPenduduk();

    $('#rw').on('change', function() {
        $('#tabel-container').prop('hidden', true)
        siapkanFilterRT($('#rw').val())
        siapkanHalaman($('#cari').val(), $('#rw').val(), $('#rt').val());
        $('#rt').val('')
        $('#cari').val('')
        $('#halaman').val(1)
        tampilkanTabelPenduduk($('#cari').val(), $('#rw').val(), $('#rt').val(), $('#halaman').val(), 1);
    });

    $('#rt').on('change', function() {
        $('#tabel-container').prop('hidden', true)
        siapkanHalaman($('#cari').val(), $('#rw').val(), $('#rt').val());
        $('#cari').val('')
        $('#halaman').val(1)
        tampilkanTabelPenduduk($('#cari').val(), $('#rw').val(), $('#rt').val(), $('#halaman').val(), 1);
    });

    $('#halaman').on('change', function() {
        $('#tabel-container').prop('hidden', true)
        $('#cari').val('')
        tampilkanTabelPenduduk($('#cari').val(), $('#rw').val(), $('#rt').val(), $('#halaman').val(), 1);
    });

    $('#cari').on('keyup', function() {
        siapkanHalaman($('#cari').val(), $('#rw').val(), $('#rt').val());
        $('#halaman').val(1)
        tampilkanTabelPenduduk($('#cari').val(), $('#rw').val(), $('#rt').val(), $('#halaman').val(), 1);
    })

});