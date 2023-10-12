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
                            <div class="col-sm-7">
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-lg-3 col-form-label">Tgl Surat</label>
                                    <div class="col-sm-9 col-lg-9">
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
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-lg-3 col-form-label">Dari</label>
                                    <div class="col-sm-9 col-lg-9">
                                        <input type="text" readonly
                                               class="form-control @error('id_opd_fk') is-invalid @enderror"
                                               value="{{cek_opd($id_opd_fk)->nama_opd}}">
                                        <input type="hidden" readonly class="form-control" name="id_opd_fk"
                                               id="id_opd_fk" value="{{$id_opd_fk}}">
                                        @error('id_opd_fk')
                                        <div class="invalid-feedback">
                                            {{$message}}
                                        </div>
                                        @enderror
                                    </div>
                                </div>


                                <div class="row mb-3">
                                    <label class="col-sm-3 col-lg-3 col-form-label">Tujuan</label>
                                    <div class="col-sm-9 col-lg-9">
                                        <div class="form-check form-check-inline mb-3">
                                            <input class="form-check-input" type="radio" id="inlineradio1"
                                                   name="tujuan" value="dalam"
                                                   onchange="cektujuan()" @if($kepada_id_opd!=null) checked @endif>
                                            <label class="form-check-label" for="inlineradio1">Antar Instansi</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" id="inlineradio2"
                                                   name="tujuan" value="luar"
                                                   onchange="cektujuan()" @if($kepada_id_opd==null) checked @endif>
                                            <label class="form-check-label" for="inlineradio2">Luar Instansi</label>
                                        </div>

                                        <input class="form-control @error('kepada') is-invalid @enderror"
                                               name="kepada" id="kepada" style="display: none"
                                               type="text" value="{{$kepada}}" autofocus>
                                        <div id="divkepadaselect">
                                            <select class="select_cari form-control" id="kepada_id_opd"
                                                    name="kepada_id_opd[]" multiple>
                                                @foreach($listPerangkat as $nama => $value)
                                                    <option
                                                        value="{{$value.'|'.$nama}}" {{array_search($value, explode (",", $kepada_id_opd)) !== false ? 'selected' : ''}}>{{$nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('kepada')
                                        <div class="invalid-feedback">
                                            {{$message}}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-lg-3 col-form-label">Lampiran</label>
                                    <div class="col-sm-9 col-lg-9">
                                        <div class="input-group">
                                 <span
                                     class="input-group-prepend">
                                 <label
                                     class="input-group-text">
                                 <i class="fa fa-clipboard-list"></i></label>
                                 </span>
                                            <input class="form-control @error('lampiran') is-invalid @enderror"
                                                   name="lampiran" id="lampiran"
                                                   type="number" value="{{$lampiran}}">
                                        </div>
                                        @error('lampiran')
                                        <div class="invalid-feedback">
                                            {{$message}}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-lg-3 col-form-label">Hal</label>
                                    <div class="col-sm-9 col-lg-9">
                                        <div class="input-group">
                                 <span
                                     class="input-group-prepend">
                                 <label
                                     class="input-group-text">
                                 <i class="fa fa-align-right"></i></label>
                                 </span>
                                            <input class="form-control @error('perihal') is-invalid @enderror"
                                                   name="perihal" id="perihal"
                                                   type="text" value="{{$perihal}}">
                                        </div>
                                        @error('perihal')
                                        <div class="invalid-feedback">
                                            {{$message}}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-lg-3 col-form-label">Bagikan ke adpim</label>
                                    <div class="col-sm-9 col-lg-9">
                                        <select class="form-control" id="bagikan_tu" name="bagikan_tu">
                                            <option value="ya" {{$bagikan_tu=='ya' ? 'selected' : ''}}>YA</option>
                                            <option value="tidak" {{$bagikan_tu=='tidak' ? 'selected' : ''}}>TIDAK
                                            </option>
                                        </select>
                                        <ul>
                                            <li>
                                                pilih <strong>YA</strong>, jika surat akan ditandatangani oleh Pejabat
                                                (Sekretaris
                                                Daerah/Asisten 1/Asisten 2/Asisten 3/Bupati/Wakil Bupati)
                                            </li>
                                            <li>
                                                pilih <strong>TIDAK</strong>, jika surat akan ditandatangani oleh Kepala
                                                Perangkat Daerah dan No Surat diisi oleh admin Perangkat Daerah
                                            </li>
                                        </ul>

                                        @error('bagikan_tu')
                                        <div class="invalid-feedback">
                                            {{$message}}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-5">
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

                                <div class="form-group" id="div_jenisttd">
                                    <label>Ditandatangani oleh</label>
                                    <select class="select_cari form-control" id="id_jenis_ttd_fk"
                                            name="id_jenis_ttd_fk"
                                            @if($kategori_ttd=='elektronik' && $status_sk=='final') disabled @endif>
                                        @foreach($listJenis as $jenis)
                                            <option
                                                value={{$jenis->id_jenis_ttd}} {{$jenis->id_jenis_ttd==$id_jenis_ttd_fk ? 'selected' : ''}}>{{$jenis->jenis_ttd.' - '.$jenis->nama_opd}}</option>
                                        @endforeach
                                    </select>
                                    @error('id_jenis_ttd_fk')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group" id="divBerkas">
                                    <label>@if($berkas) Ubah @else
                                            Unggah @endif Berkas</label>
                                    <input name="berkas" id="berkas" type="file"
                                           class="form-control"
                                           accept="application/pdf">
                                    @if($berkas)
                                        <br/>

                                        <a href="{{url('berkas/'.$berkas)}}" target="_blank">Lihat Berkas saat
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
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right bg-whitesmoke">
                        @if($mode=='tambah')
                            <button type="reset" class="btn btn-secondary mr-2">Reset Form</button>
                        @endif
                        <button type="submit" class="btn btn-primary mr-2"><i class="mr-50 fa fa-save"></i>
                            @if($mode=='ubah') Simpan Perubahan @else Submit @endif
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


        if (jQuery().daterangepicker) {
            if ($("#tgl_surat").length) {
                $('#tgl_surat').daterangepicker({
                    locale: {format: 'DD/MM/YYYY'},
                    singleDatePicker: true,
                });
            }
        }

        $(document).ready(function () {
            listTTD();
            cekpembagian();
            cektujuan();
        });


        function listTTD() {
            var kategori = $('#kategori_ttd').val();
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

        $('#bagikan_tu').change(function () {
            cekpembagian();
        })

        function cekpembagian() {
            if ($('#bagikan_tu').val() === 'ya') {
                $('#div_nosurat').addClass('d-none')
                $('#div_tandatangan').addClass('d-none')
                $('#div_jenisttd').addClass('d-none')
            } else {
                $('#div_nosurat').removeClass('d-none')
                $('#div_tandatangan').removeClass('d-none')
                $('#div_jenisttd').removeClass('d-none')
            }
        }

        function cektujuan() {
            var cektujuan = $('input[name="tujuan"]:checked').val();
            // alert(cektujuan);
            if (cektujuan == 'luar') {
                $('#kepada').show();
                $('#divkepadaselect').addClass('d-none');
            } else {
                //alert('hei');
                $('#kepada').text('');
                $('#kepada').hide();
                $('#divkepadaselect').removeClass('d-none');
            }
        }

        //cekpembagian();
    </script>
@endpush
