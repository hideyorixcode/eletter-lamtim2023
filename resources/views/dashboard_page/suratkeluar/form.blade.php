@extends('mylayouts.app')
@section('title', 'Form '.ucwords($mode).' Surat Keluar ')
@push('vendor-css')
    <link rel="stylesheet" href="{{ assetku('magnific-popup/magnific-popup.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets//modules/bootstrap-daterangepicker/daterangepicker.css')}}">

    <style>
        .select_sm {
            height: 33.22222px !important;
            padding-bottom: 2px !important;
            padding-top: 2px !important;
            padding-right: 2px !important;
            padding-left: 2px !important;
        }

        #the-canvas {
            border: 1px solid black;
        }
    </style>
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{'Form '.ucwords($mode).' Surat Keluar '}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{route('surat-keluar')}}">Daftar Surat Keluar </a></div>
                <div class="breadcrumb-item active">{{'Form '.ucwords($mode).' Surat Keluar '}}</div>
            </div>
        </div>
        <div class="section-body">
            @component('components.infoubahversipdf')
            @endcomponent
            @if (count($errors) > 0)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="alert-body">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            @endif
            <form id="form" name="form" role="form" action="{{$action}}"
                  enctype="multipart/form-data" method="post">
                {{csrf_field()}}
                @if($mode=='ubah')
                    {{ method_field('PUT') }}
                @endif
                <input name="diisi_oleh" id="diisi_oleh" type="hidden" value="{{$diisi_oleh}}">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_nosurat">
                                            <label>No Surat</label>
                                            <div class="input-group">
                                                     <span
                                                         class="input-group-prepend">
                                                     <label
                                                         class="input-group-text">
                                                     <i class="fa fa-list"></i></label>
                                                     </span>
                                                <input class="form-control @error('no_surat') is-invalid @enderror"
                                                       name="no_surat" id="no_surat"
                                                       type="text" value="{{$no_surat}}">
                                            </div>
                                            @error('no_surat')
                                            <div class="invalid-feedback">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Tgl Surat</label>

                                            <div class="input-group">
                                                        <span class="input-group-prepend">
                                                        <label class="input-group-text">
                                                        <i class="fa fa-calendar"></i></label>
                                                        </span>
                                                <input class="form-control @error('tgl_surat') is-invalid @enderror"
                                                       name="tgl_surat" id="tgl_surat"
                                                       type="text" value="{{$tgl_surat}}">
                                            </div>
                                            @error('tgl_surat')
                                            <div class="invalid-feedback">
                                                {{$message}}
                                            </div>
                                            @enderror

                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>Lampiran</label>


                                            <input class="form-control @error('lampiran') is-invalid @enderror"
                                                   name="lampiran" id="lampiran"
                                                   type="number" value="{{$lampiran}}">

                                            @error('lampiran')
                                            <div class="invalid-feedback">
                                                {{$message}}
                                            </div>
                                            @enderror

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Dari</label>

                                    <select class="select_cari form-control" id="id_opd_fk"
                                            name="id_opd_fk">
                                        @foreach($listPerangkat as $nama => $value)
                                            <option
                                                value={{$value}} {{$value==$id_opd_fk ? 'selected' : ''}}>{{$nama}}</option>
                                        @endforeach
                                    </select>
                                    @error('id_opd_fk')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror

                                </div>
                                <div class="form-group">
                                    <label>Tujuan</label>
                                    <br/>
                                    <div class="form-check form-check-inline mb-3">
                                        <input class="form-check-input" type="radio" id="inlineradio1"
                                               name="tujuan" value="dalam"
                                               onchange="cektujuan()" @if($tujuan=='dalam') checked @endif>
                                        <label class="form-check-label" for="inlineradio1">Antar Instansi</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="inlineradio2"
                                               name="tujuan" value="luar"
                                               onchange="cektujuan()"
                                               @if($tujuan=='luar' || $tujuan=='') checked @endif>
                                        <label class="form-check-label" for="inlineradio2">Lainnya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="inlineradio3"
                                               name="tujuan" value="keduanya"
                                               onchange="cektujuan()" @if($tujuan=='keduanya') checked @endif>
                                        <label class="form-check-label" for="inlineradio3">Atur Keduanya</label>
                                    </div>
                                    <div id="divkepadaselect">
                                        <select class="select_cari_placeholder form-control" id="kepada_id_opd"
                                                name="kepada_id_opd[]" multiple>
                                            @foreach($listPerangkat as $nama => $value)
                                                <option
                                                    value="{{$value.';'.$nama}}" {{array_search($value, explode (",", $kepada_id_opd)) !== false ? 'selected' : ''}}>{{$nama}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <input class="form-control @error('kepada') is-invalid @enderror"
                                           name="kepada" id="kepada"
                                           type="text" value="{{$kepada}}"
                                           placeholder="Ketik Tujuan Lainnya jika lebih dari 1 gunakan tanda titik koma (;)">
                                    @error('kepada')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror

                                </div>

                                <div class="form-group">
                                    <label>Hal</label>

                                    <div class="input-group">
                                                        <span
                                                            class="input-group-prepend">
                                                        <label
                                                            class="input-group-text">
                                                        <i class="fa fa-align-right"></i></label>
                                                        </span>
                                        <input class="form-control @error('perihal') is-invalid @enderror"
                                               name="perihal" id="perihal"
                                               type="text" value="{{$perihal}}" autofocus>
                                    </div>
                                    @error('perihal')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>

                            </div>
                            <div class="col-sm-6">


                                <div class="form-group">
                                    <label>Ditandatangani oleh</label>
                                    <div id="div_jenisttd">
                                        @if($status_sk=='final' && $berkas!=null)
                                            <input type="hidden" id="id_jenis_ttd_fk" name="id_jenis_ttd_fk"
                                                   value="{{$id_jenis_ttd_fk}}">
                                            <input type="text" class="form-control" readonly
                                                   value="{{cek_ttd($id_jenis_ttd_fk)->jenis_ttd}}"
                                        @else
                                            <select class="select_cari form-control" id="id_jenis_ttd_fk"
                                                    name="id_jenis_ttd_fk">
                                                @foreach($listJenis as $jenis)
                                                    <option
                                                        value={{$jenis->id_jenis_ttd}} {{$jenis->id_jenis_ttd==$id_jenis_ttd_fk ? 'selected' : ''}}>{{$jenis->jenis_ttd.' - '.$jenis->nama_opd}}</option>
                                                @endforeach
                                            </select>
                                        @endif

                                    </div>
                                    @error('id_jenis_ttd_fk')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group" id="divBerkas">
                                    {{--                                     @if($status_sk=='final') style="display: none" @endif>--}}
                                    <label>@if($berkas)
                                            Ubah
                                        @else
                                            Unggah
                                        @endif Berkas</label>
                                    <input name="berkas" id="berkas" type="file"
                                           class="form-control"
                                           accept="application/pdf">
                                    @if($berkas)
                                        <br/>
                                        <a href="{{url('berkas/'.$berkas)}}" target="_blank">Lihat Berkas
                                            saat
                                            ini</a>
                                        <br/>
                                        <input type="checkbox" name="remove_berkas"
                                               value="{{$berkas}}">
                                        Hapus
                                        Berkas
                                        Ketika Disimpan
                                    @endif
                                    @error('berkas')
                                    <p style="color:red">
                                        {{$message}}
                                    </p>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Akses Download</label>
                                    <select class="form-control" name="is_download" id="is_download">
                                        <option value=1 {{$is_download==1 ? 'selected' : ''}}>YA</option>
                                        <option value=0 {{$is_download==0 ? 'selected' : ''}}>TIDAK</option>
                                    </select>
                                    @error('is_download')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                                @if($mode=='ubah' && $diisi_oleh=='perangkat_daerah')
                                    <div class="form-group">
                                        <label>Status</label>
                                        <br/>
                                        <div class="form-check form-check-inline mb-3">
                                            <input class="form-check-input" type="radio" id="statusradio1"
                                                   name="status_sk" value="draft" onchange="cekstatus()"
                                                   @if($status_sk=='draft') checked @endif>
                                            <label class="form-check-label" for="statusradio1">DRAFT</label>
                                        </div>
                                        <div class="form-check form-check-inline mb-3">
                                            <input class="form-check-input" type="radio" id="statusradio2"
                                                   name="status_sk" value="revisi" onchange="cekstatus()"
                                                   @if($status_sk=='revisi') checked @endif>
                                            <label class="form-check-label" for="statusradio2">REVISI</label>
                                        </div>
                                        <div class="form-check form-check-inline mb-3">
                                            <input class="form-check-input" type="radio" id="statusradio3"
                                                   name="status_sk" value="final" onchange="cekstatus()"
                                                   @if($status_sk=='final') checked @endif>
                                            <label class="form-check-label" for="statusradio3">FINAL</label>
                                        </div>

                                    </div>
                                @endif
                            </div>
                            @if($mode=='ubah' && $diisi_oleh=='perangkat_daerah')
                                <div class="col-sm-12" id="divcatatan">

                                    <div class="col-sm-12 mt-4 border-bottom-0">
                                        <div id="accordion">
                                            <div class="accordion">
                                                <div class="accordion-header" role="button" data-toggle="collapse"
                                                     data-target="#panel-body-1" aria-expanded="true">
                                                    <h4>Catatan dari Protokol Pimpinan</h4>
                                                </div>
                                                <div class="accordion-body collapse show" id="panel-body-1"
                                                     data-parent="#accordion">
                                                    <textarea
                                                        class="form-control"
                                                        name="catatan"
                                                        id="catatan"
                                                        style="min-height: 100px">{{$catatan}}</textarea>
                                                </div>
                                            </div>
                                            @if($tanggapan!=null)
                                                <div class="accordion">
                                                    <div class="accordion-header" role="button" data-toggle="collapse"
                                                         data-target="#panel-body-2">
                                                        <h4>Tanggapan terhadap catatan yang diberikan</h4>
                                                    </div>
                                                    <div class="accordion-body collapse show" id="panel-body-2"
                                                         data-parent="#accordion">
                                                        <p class="mb-0">{{$tanggapan}}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            @endif
                        </div>
                    </div>


                    <div class="card-footer text-right bg-whitesmoke">
                        @if($mode=='tambah')
                            <button type="reset" class="btn btn-secondary mr-2">Reset Form</button>
                        @endif
                        <button type="submit" class="btn btn-primary mr-2"><i class="mr-50 fa fa-save"></i>
                            @if($mode=='ubah')
                                Simpan Perubahan
                            @else
                                Submit
                            @endif
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
@push('scripts')
    <script src="{{assetku('assets/modules/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{assetku('assets/modules/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{ assetku('magnific-popup/jquery.magnific-popup.min.js')}}"></script>
    <script src="{{ assetku('assets/modules/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>

    <!--begin::Page Scripts(used by this page)-->
    <script type="text/javascript">
        @if(session('pesan_status'))
        tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
        @endif

        $(document).ready(function () {
            listTTD();
            cektujuan();
            cekstatus();
        });

        if (jQuery().daterangepicker) {
            if ($("#tgl_surat").length) {
                $('#tgl_surat').daterangepicker({
                    locale: {format: 'DD/MM/YYYY'},
                    singleDatePicker: true,
                });
            }
        }


        if (jQuery().select2) {
            $(".select_cari_placeholder").select2(
                {
                    placeholder: "Pilih Perangkat Daerah (bisa lebih dari 1 perangkat daerah)",
                    allowClear: true
                }
            );
        }

        function listTTD() {
            var kategori = 'basah'
            $.ajax({
                url: '{{ url('dashboard/get-list-ttd') }}',
                type: 'GET',
                data: {
                    kategori: kategori,
                    id_jenis_ttd_fk: '{{$id_jenis_ttd_fk}}',
                },
                success: function (res) {
                    $('select[name="id_jenis_ttd_fk"]').html(res);
                },
                error(res) {
                    console.log(res);
                }
            });
        }

        function cektujuan() {
            var cektujuan = $('input[name="tujuan"]:checked').val();
            // alert(cektujuan);
            if (cektujuan == 'luar') {
                $('#kepada').show();
                $('#divkepadaselect').addClass('d-none');
            } else if (cektujuan == 'dalam') {
                $('#kepada').hide();
                $('#divkepadaselect').removeClass('d-none');
            } else {
                //alert('hei');
                //$('#kepada').text('');
                $('#kepada').show();
                $('#divkepadaselect').removeClass('d-none');
            }
        }

        function cekstatus() {
            var cekstatus = $('input[name="status_sk"]:checked').val();
            // alert(cektujuan);
            if (cekstatus == 'draft') {
                $('#divcatatan').hide();

            } else {
                //alert('hei');
                //$('#kepada').text('');
                $('#divcatatan').show();
            }
        }

    </script>
@endpush
