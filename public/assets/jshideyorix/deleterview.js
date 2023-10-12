function deleteDataView(paramId, urlnya) {
    var id = paramId;
    var token = $('meta[name="csrf-token"]').attr('content');
    Swal.fire({
        title: 'Apakah anda ingin menghapus data ini',
        text: "Data yang anda hapus, tidak akan kembali",
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
                    type: 'DELETE',
                    dataType: "JSON",
                    data: {
                        "id": id,
                    },
                    success: function (response) {
                        if (response.status) {
                            getViewData(1);
                            iziToast.success({
                                title: 'Sukses',
                                message: response.pesan,
                                position: 'topRight'
                            });
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: response.pesan,
                                position: 'topRight'
                            });
                        }
                    },
                    error: function (xhr) {
                        iziToast.error({
                            title: 'Error',
                            message: xhr.responseText,
                            position: 'topRight'
                        });
                    }
                });

        } else if (result.dismiss === "cancel") {
            iziToast.info({
                title: 'Info',
                message: 'data dibatalkan untuk dihapus',
                position: 'topRight'
            });
        }
    });
}


function bulkDeleteView(urlnya) {
    var list_id = [];
    $(".data-check:checked").each(function () {
        list_id.push(this.value);
    });
    var token = $('meta[name="csrf-token"]').attr('content');
    if (list_id.length > 0) {
        Swal.fire({
            title: 'Yakin akan menghapus : ' + list_id.length + ' data yg telah dipilih ?',
            text: "Cek kembali data anda sebelum dihapus",
            icon: 'warning',
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
                    },
                    dataType: "JSON",
                    success: function (data) {
                        if (data.status) {
                            getViewData(1);
                            iziToast.success({
                                title: 'Sukses',
                                message: 'Berhasil Hapus ' + list_id.length + ' data',
                                position: 'topRight'
                            });
                            $('#check-all').prop('checked', false); // Unchecks
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: 'Gagal Hapus ' + list_id.length + ' data',
                                position: 'topRight'
                            });
                        }
                    },
                    error: function (xhr) {
                        iziToast.error({
                            title: 'Error',
                            message: xhr.responseText,
                            position: 'topRight'
                        });
                    }
                });
            } else if (result.dismiss === "cancel") {
                iziToast.info({
                    title: 'Info',
                    message: 'data dibatalkan untuk dihapus',
                    position: 'topRight'
                });
            }
        });
    } else {
        iziToast.warning({
            title: 'Perhatian',
            message: 'Silahkan pilih data yang akan dihapus',
            position: 'topRight'
        });
    }
}
