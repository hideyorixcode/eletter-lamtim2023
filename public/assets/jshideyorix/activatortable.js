function active(paramId, urlnya, mode, teks) {
    var id = paramId;
    var token = $('meta[name="csrf-token"]').attr('content');
    Swal.fire({
        title: 'Apakah anda ingin ' + teks + ' data ini',
        text: "cek kembali data anda",
        icon: 'warning',
        showCancelButton: true,
        reverseButtons: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak',
    }).then((result) => {
        if (result.value) {
            $.ajax(
                {
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    url: urlnya + '/' + id,
                    type: 'GET',
                    dataType: "JSON",
                    data: {
                        id: id,
                        mode: mode,
                        teks: teks,
                    },
                    success: function (response) {
                        if (response.status) {
                            reloadTable();
                            iziToast.success('berhasil ' + teks, 'Sukses');
                        } else {
                            iziToast.error('gagal ' + teks, 'Error');
                        }
                    },
                    error: function (xhr) {
                        iziToast.error(xhr.responseText, 'Error');
                    }
                });

        } else if (result.dismiss === "cancel") {
            iziToast.info('data dibatalkan untuk ' + teks, 'Info');
        }
    });
}


function bulkActive(mode, urlnya, teks) {
    var list_id = [];
    $(".data-check:checked").each(function () {
        list_id.push(this.value);
    });
    var token = $('meta[name="csrf-token"]').attr('content');
    if (list_id.length > 0) {
        Swal.fire({
            title: 'Yakin akan ' + teks + ' : ' + list_id.length + ' data yg telah dipilih ?',
            text: "Cek kembali data anda",
            type: "warning",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak',
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    url: urlnya,
                    type: "POST",
                    data: {
                        id: list_id,
                        mode: mode,
                        teks: teks,
                    },
                    dataType: "JSON",
                    success: function (data) {
                        if (data.status) {
                            reloadTable();
                            iziToast.success('Berhasil ' + teks + ' ' + list_id.length + ' data', 'Sukses');
                            $('#check-all').prop('checked', false); // Unchecks
                        } else {
                            iziToast.error('Gagal ' + teks + ' ' + list_id.length + ' data', 'Error');
                        }
                    },
                    error: function (xhr) {
                        iziToast.error(xhr.responseText, 'Error');
                    }
                });
            } else if (result.dismiss === "cancel") {
                iziToast.info('data dibatalkan untuk ' + teks, 'Info');
            }
        });
    } else {
        iziToast.warning('Silahkan pilih data yang akan' + teks, 'Perhatian');
    }
}
