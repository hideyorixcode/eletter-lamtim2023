@if(count($listDisposisi)>0)
    @if($bolehSimpan==true)
        <form id="form" name="form" method="post" enctype="multipart/form-data"> @endif
            <input type="hidden" id="id_sm_fk" name="id_sm_fk" value="{{$id_sm_fk}}">
            <div class="card">
                <div class="card-header">
                    <h4>Disposisi Surat di Lingkungan {{getSetting('area')}}</h4>

                    @if($bolehSimpan==true)
                        <div class="card-header-action">
                            <button class="btn btn-success" type="submit">
                                Simpan Perubahan Disposisi
                            </button>
                        </div>
                    @endif

                </div>

                <div class="card-body">


                    <div class="activities">
                        <?php $no = 1;?>
                        @foreach($listDisposisi as $data)

                            <?php

                            if ($data->tgl_diterima) :
                                $input_tgl = TanggalIndowaktu($data->tgl_diterima);
                            else :
                                $input_tgl = '';
                            endif;

                            if ($data->status == 'diteruskan'):
                                $status = 'info';
                                $icon = 'fas fa-sign-in-alt';
                            elseif ($data->status == 'diolah'):
                                $status = 'success';
                                $icon = 'fas fa-lock';
                            else:
                                $status = 'default';
                                $icon = 'fas fa-edit';
                            endif;
                            ?>
                            @if($data->penerima==Auth::user()->id_opd_fk)
                                <input type="hidden" id="id" name="id[]"
                                       value="{{$data->id}}">
                                @if($input_tgl!=null)
                                    @include('dashboard_page.suratmasuk.pengisianedit')
                                @else
                                    @include('dashboard_page.suratmasuk.pengisian')
                                @endif
                                    @php $no++; @endphp
                            @else
                                @if($input_tgl!=null)
                                    @include('dashboard_page.suratmasuk.tanggalisiuser')
                                @else
                                    @include('dashboard_page.suratmasuk.tanggalkosonguser')
                                @endif
                            @endif

                        @endforeach
                    </div>

                </div>
            </div>
            @if($bolehSimpan==true)</form>@endif

    <script>
        function check_status(no) {
            var elemen_no = $('#status_' + no).val();
            if (elemen_no == "diolah") {
                $('#div_kepada_' + no).hide();
                $('#div_melalui_' + no).hide();
                $('#div_catatan_' + no).hide();
                $('#kepada_' + no).prop('required', false);
                $('#tgl_diterima_' + no).prop('required', false);
                $('#nama_penerima_' + no).prop('required', false);
            } else {
                $('#div_kepada_' + no).show();
                $('#kepada_' + no).prop('required', true);
                $('#div_melalui_' + no).show();
                $('#div_catatan_' + no).show();
                $('#tgl_diterima_' + no).prop('required', true);
                $('#nama_penerima_' + no).prop('required', true);
            }
        }

        function reset_tanggal(no) {
            $('#tgl_diterima_' + no).val('');
        }

        function required_kepada(no) {
            var elemen_no = $('#status_' + no).val();
            if (elemen_no == "diolah") {
                $('#kepada_' + no).attr('required', '');
            } else {
                $('#kepada_' + no).attr('required', true);
            }

        }

        function set_tanggal(no) {
            var today = new Date();
            // var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
            var tanggalbener = today.getDate() < 10 ? '0' + today.getDate() : today.getDate();
            var bulanawal = today.getMonth()+1;
             var bulanbener = bulanawal < 10 ? '0' + bulanawal : bulanawal;
            var date = tanggalbener + '/' + (bulanbener) + '/' + today.getFullYear();
            // var date = tanggalbener + '/' + (today.getMonth() + 1) + '/' + today.getFullYear();
            var jamnya = today.getHours() < 10 ? '0' + today.getHours() : today.getHours();
            var menit = today.getMinutes() < 10 ? '0' + today.getMinutes() : today.getMinutes();
            $('#tgl_diterima_' + no).val(date + ' ' + jamnya + ":" + menit);
        }

        var frm = $('#form');
        frm.submit(function (ev) {
            Swal.fire({
                title: "Simpan Perubahan Penerimaan Surat Masuk",
                text: "Pastikan kembali data-data yang telah anda isi dengan benar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan Perubahan!',
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    var url;
                    var formData = new FormData($('#form')[0]);
                    url = "{{ url('dashboard/disposisi/update-disposisi/') }}";
                    var token = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        url: url,
                        type: 'POST',
                        data: formData,
                        dataType: "JSON",
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            if (data.status) //if success close modal and reload ajax table
                            {
                                getViewData();
                                iziToast.success({
                                    title: 'Sukses',
                                    message: 'Berhasil Simpan Perubahan Disposisi',
                                    position: 'topRight'
                                });
                                $('#modal_form').modal('hide');

                            } else {
                                for (var i = 0; i < data.inputerror.length; i++) {
                                    $('[name="' + data.inputerror[i] + '"]').addClass('is-invalid'); //select parent twice to
                                    $('#error_' + data.inputerror[i] + '').text(data.error_string[i]);
                                    $('[name="' + data.inputerror[i] + '"]').focus();
                                }
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
                }
            })
            ev.preventDefault();
        });


    </script>
@else
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <div class="alert-body">
            TIDAK DITEMUKAN DATA DISPOSISI
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
@endif
