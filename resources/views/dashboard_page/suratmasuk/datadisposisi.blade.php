@if(count($listDisposisi)>0)
    <form id="form" name="form" method="post" enctype="multipart/form-data">
        <input type="hidden" id="id_sm_fk" name="id_sm_fk" value="{{$id_sm_fk}}">
        <div class="card">
            <div class="card-header">
                <h4>Disposisi Surat di Lingkungan {{getSetting('area')}}</h4>

                <div class="card-header-action">
                    <button class="btn btn-success" type="submit">
                        Simpan Perubahan Disposisi
                    </button>
                </div>

            </div>

            <div class="card-body">


                <div class="activities">
                    @foreach($listDisposisi as $data)
                        <input type="hidden" id="id" name="id[]" value="{{$data->id}}">
                        <?php
                        $no = 1;
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
                        <div class="activity">
                            <div class="activity-icon bg-{{$status}} text-white shadow-{{$status}}">
                                <i class="{{$icon}}"></i>
                            </div>
                            <div class="activity-detail" style="width:100%!important; overflow: auto">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Tanggal Diterima</label>
                                            @if($input_tgl!=null)
                                                <input class="datetimepickerindo form-control"
                                                       name="tgl_diterima_{{$loop->iteration}}"
                                                       id="tgl_diterima_{{$loop->iteration}}" required
                                                       type="text"
                                                       value="{{$input_tgl}}"/>
                                            @else
                                                <input class="datetimepickerindokosong form-control"
                                                       name="tgl_diterima_{{$loop->iteration}}"
                                                       id="tgl_diterima_{{$loop->iteration}}"
                                                       {{$data->status=='diteruskan' ? 'required' : ''}}
                                                       type="text"
                                                       value="{{$input_tgl}}" autocomplete="off"/>
                                                <button onclick="set_tanggal({{$loop->iteration}})"
                                                        class="btn btn-sm btn-outline-primary mt-2" type="button">Set
                                                    Tgl
                                                    dan
                                                    Waktu
                                                </button>

                                                <button onclick="reset_tanggal({{$loop->iteration}})"
                                                        class="btn btn-sm btn-outline-danger mt-2" type="button">Reset
                                                    Tanggal
                                                </button>
                                            @endif
                                            <div class="invalid-feedback" id="error_tgl_diterima_{{$loop->iteration}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Diterima oleh</label>
                                            <select id="penerima" name="penerima_{{$loop->iteration}}"
                                                    class="form-control select_cari" required>
                                                @foreach($listPerangkat as $nama => $value)
                                                    <option
                                                        value="{{$value}}" {{$value==$data->penerima ? 'selected' : ''}}>{{$nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Nama Penerima</label>
                                            <input class="form-control"
                                                   name="nama_penerima_{{$loop->iteration}}" id="nama_penerima"
                                                   value="{{$data->nama_penerima}}" required
                                                   placeholder="Nama Penerima">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select id="status_{{$loop->iteration}}" name="status_{{$loop->iteration}}"
                                                    onchange="check_status({{$loop->iteration}})"
                                                    class="form-control" required>
                                                @if(cekJenisOPD($data->penerima)!='opd')
                                                    <option
                                                        value="diteruskan" {{$data->status=='diteruskan' ? 'selected' : ''}}>
                                                        DITERUSKAN
                                                    </option>
                                                @endif
                                                <option value="diolah" {{$data->status=='diolah' ? 'selected' : ''}}>
                                                    DIOLAH
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4" id="div_kepada_{{$loop->iteration}}"
                                         @if($data->status=='diolah') style="display: none" @endif>
                                        <div class="form-group">
                                            <label>Kepada</label>

                                            <select id="kepada_{{$loop->iteration}}"
                                                    name="kepada_{{$loop->iteration}}[]"
                                                    class="form-control select_cari"
                                                    multiple {{$data->status=='diteruskan' ? 'required' : ''}}>
                                                @foreach($listPerangkat as $nama => $value)
                                                    <option
                                                        value="{{$value}}" {{array_search($value, explode (",", $data->kepada)) !== false ? 'selected' : ''}}>{{$nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4" id="div_melalui_{{$loop->iteration}}"
                                         @if($data->status=='diolah') style="display: none" @endif>
                                        <div class="form-group">
                                            <label>Melalui/Langsung</label>
                                            <select id="melalui_id_opd"
                                                    name="melalui_id_opd_{{$loop->iteration}}"
                                                    class="form-control select_cari"
                                                    @if($data->status=='diteruskan') required @endif>

                                                @foreach($listTu as $nama => $value)
                                                    <option
                                                        value="{{$value}}" {{$value==$data->melalui_id_opd ? 'selected' : ''}}>{{$nama}}</option>
                                                @endforeach
                                                <option value="" {{$data->melalui_id_opd==null ? 'selected' : ''}}>
                                                    Langsung Tanpa Perantara
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="div_catatan_{{$loop->iteration}}"
                                     @if($data->status=='diolah') style="display: none" @endif>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Catatan</label>


                                            <textarea class="form-control"
                                                      style="min-height: 100px;"
                                                      rows="4"
                                                      placeholder="Ketik Catatan Disposisi..."
                                                      name="catatan_disposisi_{{$loop->iteration}}"
                                                      id="catatan_disposisi_{{$loop->iteration}}">{{$data->catatan_disposisi}}</textarea>

                                        </div>
                                    </div>
                                </div>
                                @if($input_tgl==null)
                                    <button onclick="batalkan_disposisi('{{$data->id}}')"
                                            class="btn btn-sm btn-danger mt-2" type="button"><i class="fa fa-times"></i>
                                        Batalkan Disposisi
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </form>

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
                    url = "{{ url('dashboard/disposisi/create/') }}";
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
