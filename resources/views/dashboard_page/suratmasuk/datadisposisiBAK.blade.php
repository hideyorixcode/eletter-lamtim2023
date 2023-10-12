<style>
    .timeline {
        list-style-type: none;
        margin: 0;
        padding: 0;
        position: relative
    }

    .timeline:before {
        content: '';
        position: absolute;
        top: 5px;
        bottom: 5px;
        width: 5px;
        background: #2d353c;
        left: 20%;
        margin-left: -2.5px
    }

    .timeline > li {
        position: relative;
        min-height: 50px;
        padding: 20px 0
    }

    .timeline .timeline-time {
        position: absolute;
        left: 0;
        width: 18%;
        text-align: right;
        top: 30px
    }

    .timeline .timeline-time .date,
    .timeline .timeline-time .time {
        display: block;
        font-weight: 600
    }

    .timeline .timeline-time .date {
        line-height: 16px;
        font-size: 12px
    }

    .timeline .timeline-time .time {
        line-height: 18px;
        font-size: 16px;
        color: #242a30
    }

    .timeline .timeline-icon {
        left: 15%;
        position: absolute;
        width: 10%;
        text-align: center;
        top: 40px
    }

    .timeline .timeline-icon a {
        text-decoration: none;
        width: 20px;
        height: 20px;
        display: inline-block;
        border-radius: 20px;
        background: #d9e0e7;
        line-height: 10px;
        color: #fff;
        font-size: 14px;
        border: 5px solid #2d353c;
        transition: border-color .2s linear
    }

    .timeline .timeline-body {
        margin-left: 23%;
        margin-right: 17%;
        background: #fff;
        position: relative;
        padding: 20px 25px;
        border-radius: 6px
    }

    .timeline .timeline-body:before {
        content: '';
        display: block;
        position: absolute;
        border: 10px solid transparent;
        border-right-color: #fff;
        left: -20px;
        top: 20px
    }

    .timeline .timeline-body > div + div {
        margin-top: 15px
    }

    .timeline .timeline-body > div + div:last-child {
        margin-bottom: -20px;
        padding-bottom: 20px;
        border-radius: 0 0 6px 6px
    }

    .timeline-header {
        padding-bottom: 10px;
        border-bottom: 1px solid #e2e7eb;
        line-height: 30px
    }

    .timeline-header .userimage {
        float: left;
        width: 34px;
        height: 34px;
        border-radius: 40px;
        overflow: hidden;
        margin: -2px 10px -2px 0
    }

    .timeline-header .username {
        font-size: 16px;
        font-weight: 600
    }

    .timeline-header .username,
    .timeline-header .username a {
        color: #2d353c
    }

    .timeline img {
        max-width: 100%;
        display: block
    }

    .timeline-content {
        letter-spacing: .25px;
        line-height: 18px;
        font-size: 13px
    }

    .timeline-content:after,
    .timeline-content:before {
        content: '';
        display: table;
        clear: both
    }

    .timeline-title {
        margin-top: 0
    }

    .timeline-footer {
        background: #fff;
        border-top: 1px solid #e2e7ec;
        padding-top: 15px
    }

    .timeline-footer a:not(.btn) {
        color: #575d63
    }

    .timeline-footer a:not(.btn):focus,
    .timeline-footer a:not(.btn):hover {
        color: #2d353c
    }

    .timeline-likes {
        color: #6d767f;
        font-weight: 600;
        font-size: 12px
    }

    .timeline-likes .stats-right {
        float: right
    }

    .timeline-likes .stats-total {
        display: inline-block;
        line-height: 20px
    }

    .timeline-likes .stats-icon {
        float: left;
        margin-right: 5px;
        font-size: 9px
    }

    .timeline-likes .stats-icon + .stats-icon {
        margin-left: -2px
    }

    .timeline-likes .stats-text {
        line-height: 20px
    }

    .timeline-likes .stats-text + .stats-text {
        margin-left: 15px
    }

    .timeline-comment-box {
        background: #f2f3f4;
        margin-left: -25px;
        margin-right: -25px;
        padding: 20px 25px
    }

    .timeline-comment-box .user {
        float: left;
        width: 34px;
        height: 34px;
        overflow: hidden;
        border-radius: 30px
    }

    .timeline-comment-box .user img {
        max-width: 100%;
        max-height: 100%
    }

    .timeline-comment-box .user + .input {
        margin-left: 44px
    }

    .lead {
        margin-bottom: 20px;
        font-size: 21px;
        font-weight: 300;
        line-height: 1.4;
    }

    .text-danger, .text-red {
        color: #ff5b57 !important;
    }
</style>

@if(count($listDisposisi)>0)
    <form id="form" name="form" method="post" enctype="multipart/form-data" action="javascript:save();">
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
                <ul class="timeline">
                    @foreach($listDisposisi as $data)
                        <input type="hidden" id="id" name="id[]" value="{{$data->id}}">
                        <?php
                        $no = 1;
                        if ($data->tgl_diterima) :
                            $input_tgl = TanggalIndowaktu($data->tgl_diterima);
                        else :
                            $input_tgl = '';
                        endif;
                        ?>
                        <li>
                            <!-- begin timeline-time -->
                            <div class="timeline-time">
                                @if($input_tgl!=null)
                                    <span class="date">Tanggal Diterima</span>
                                    <span class="time"><input class="datetimepickerindo form-control"
                                                              name="tgl_diterima_{{$loop->iteration}}"
                                                              id="tgl_diterima_{{$loop->iteration}}" required
                                                              type="text"
                                                              value="{{$input_tgl}}"/></span>
                                @else
                                    <span class="date">Tanggal Diterima</span>
                                    <span class="time"><input class="datetimepickerindokosong form-control"
                                                              name="tgl_diterima_{{$loop->iteration}}"
                                                              id="tgl_diterima_{{$loop->iteration}}" {{$data->status=='diteruskan' ? 'required' : ''}}
                                                              type="text"
                                                              value="{{$input_tgl}}" autocomplete="off"/></span>

                                    <button onclick="set_tanggal({{$loop->iteration}})"
                                            class="btn btn-sm btn-outline-primary mt-2" type="button">Set Tgl dan Waktu
                                    </button>

                                    <button onclick="reset_tanggal({{$loop->iteration}})"
                                            class="btn btn-sm btn-outline-danger mt-2" type="button">Reset Tanggal
                                    </button>
                                @endif


                            </div>
                            <!-- end timeline-time -->
                            <!-- begin timeline-icon -->
                            <div class="timeline-icon">
                                <a href="javascript:;">&nbsp;</a>
                            </div>
                            <!-- end timeline-icon -->
                            <!-- begin timeline-body -->
                            <div class="timeline-body">
                                <div class="timeline-header">
                                    <div class="row">
                                        <div class="col-sm-7 col-lg-7">
                                            <select id="penerima" name="penerima_{{$loop->iteration}}"
                                                    class="form-control select_cari" required>
                                                @foreach($listPerangkat as $nama => $value)
                                                    <option
                                                        value="{{$value}}" {{$value==$data->penerima ? 'selected' : ''}}>{{'Penerima : ' .$nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-5 col-lg-5">
                                            <input class="form-control"
                                                   name="nama_penerima_{{$loop->iteration}}" id="nama_penerima"
                                                   value="{{$data->nama_penerima}}" required
                                                   placeholder="Nama Penerima">
                                        </div>
                                    </div>

                                    <span class="pull-right text-muted"></span>
                                </div>
                                <div class="timeline-content">
                                    <div class="row mb-2">
                                        <div class="col-sm-5 col-lg-5">
                                            <select id="status_{{$loop->iteration}}" name="status_{{$loop->iteration}}"
                                                    onchange="check_status({{$loop->iteration}})"
                                                    class="form-control" required>
                                                @if(cekJenisOPD($data->penerima)!='opd')
                                                    <option
                                                        value="diteruskan" {{$data->status=='diteruskan' ? 'selected' : ''}}>
                                                        DITERUSKAN kepada
                                                    </option>
                                                @endif
                                                <option value="diolah" {{$data->status=='diolah' ? 'selected' : ''}}>
                                                    DIOLAH
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-sm-7 col-lg-7" id="div_kepada_{{$loop->iteration}}"
                                             @if($data->status=='diolah') style="display: none" @endif>
                                            <select id="kepada_{{$loop->iteration}}"
                                                    name="kepada_{{$loop->iteration}}[]"
                                                    class="form-control select_cari" multiple {{$data->status=='diteruskan' ? 'required' : ''}}>
                                                @foreach($listPerangkat as $nama => $value)
                                                    <option
                                                        value="{{$value}}" {{array_search($value, explode (",", $data->kepada)) !== false ? 'selected' : ''}}>{{$nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-sm-7 col-lg-7" id="div_melalui_{{$loop->iteration}}"
                                             @if($data->status=='diolah') style="display: none" @endif>
                                            <select id="melalui_id_opd"
                                                    name="melalui_id_opd_{{$loop->iteration}}"
                                                    class="form-control select_cari" @if($data->status=='diteruskan') required @endif>

                                                @foreach($listTu as $nama => $value)
                                                    <option
                                                        value="{{$value}}" {{$value==$data->melalui_id_opd ? 'selected' : ''}}>
                                                        Melalui : {{$nama}}</option>
                                                @endforeach
                                                <option value="" {{$data->melalui_id_opd==null ? 'selected' : ''}}>
                                                    Langsung Tanpa Perantara
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="timeline-comment-box" id="div_catatan_{{$loop->iteration}}"
                                     @if($data->status=='diolah') style="display: none" @endif>
                                    <div class="input">

                                        <textarea class="form-control" style="min-height: 100px;" rows="4"
                                                  placeholder="Ketik Catatan Disposisi..."
                                                  name="catatan_disposisi_{{$loop->iteration}}"
                                                  id="catatan_disposisi_{{$loop->iteration}}">{{$data->catatan_disposisi}}</textarea>

                                    </div>
                                </div>
                                @if($input_tgl==null)
                                    <button onclick="batalkan_disposisi('{{$data->id}}')"
                                            class="btn btn-sm btn-danger mt-2" type="button"><i class="fa fa-times"></i>
                                        Batalkan Disposisi
                                    </button>
                                @endif
                            </div>
                            <!-- end timeline-body -->
                        </li>
                        <?php $no++;?>
                    @endforeach
                </ul>

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
            var date = today.getDate() + '/' + (today.getMonth() + 1) + '/' + today.getFullYear();
            var jamnya = today.getHours() < 10 ? '0' + today.getHours() : today.getHours();
            $('#tgl_diterima_' + no).val(date + ' ' + jamnya + ":" + today.getMinutes());
        }
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
