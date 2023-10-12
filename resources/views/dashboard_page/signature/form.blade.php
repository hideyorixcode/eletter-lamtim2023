@extends('mylayouts.app')
@section('title', 'Form '.ucwords($mode).' Signature QR')
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
    </style>
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{'Form '.ucwords($mode).' Signature QR'}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{route('kode-qr')}}">Daftar Signature QR</a></div>
                <div class="breadcrumb-item active">{{'Form '.ucwords($mode).' Signature QR'}}</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-sm-8 order-sm-0 order-lg-1 order-xl-1">
                    <div class="card">
                        <form id="form" name="form" role="form" action="{{$action}}"
                              enctype="multipart/form-data" method="post">
                            {{csrf_field()}}
                            @if($mode=='ubah')
                                {{ method_field('PUT') }}
                            @endif
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Judul</label>
                                        <div class="col-sm-9 col-lg-9">
                                            <div class="input-group">
                                 <span class="input-group-prepend">
                                 <label class="input-group-text">
                                 <i class="fa fa-list"></i></label>
                                 </span>
                                                <input class="form-control @error('judul') is-invalid @enderror"
                                                       required="required" name="judul" id="judul"
                                                       type="text" value="{{$judul}}" autofocus>
                                            </div>
                                            @error('judul')
                                            <div class="invalid-feedback">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Tanggal</label>
                                        <div class="col-sm-9 col-lg-9">
                                            <div class="input-group">
                                                          <span class="input-group-prepend">
                                                          <label class="input-group-text">
                                                          <i class="fa fa-calendar"></i></label>
                                                          </span>
                                                <input class="form-control @error('tgl') is-invalid @enderror"
                                                       name="tgl" id="tgl"
                                                       type="text" value="{{$tgl}}">
                                            </div>
                                            @error('tgl')
                                            <div class="invalid-feedback">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Pejabat / Perangkat
                                            Daerah</label>
                                        <div class="col-sm-9 col-lg-9">

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
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Nomor Surat</label>
                                        <div class="col-sm-9 col-lg-9">

                                            {{--                                            <select class="cari form-control" id="nomor_surat"--}}
                                            {{--                                                    name="nomor_surat">--}}

                                            {{--                                            </select>--}}

                                            <input type="text" name="nomor_surat" id="nomor_surat"
                                                   class="form-control"/>
                                            <div id="noList">
                                            </div>


                                            @error('nomor_surat')
                                            <div class="invalid-feedback">
                                                {{$message}}
                                            </div>
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
                        </form>
                    </div>
                </div>
                <div class="col-sm-4 order-sm-0 order-lg-0 order-xl-0">
                    <div class="card">
                        <div class="card-body">
                            <div class="empty-state">
                                @if($mode=='ubah')
                                    <img class="img-fluid"
                                         src="{{url('signatureqr/'.$qrcode)}}"
                                         alt="image">
                                    <h2 class="mt-2 mb-4">Signature QR saat ini</h2>
                                @else
                                    <img class="img-fluid"
                                         src="{{assetku('assets/img/drawkit/revenue-graph-colour.svg')}}"
                                         alt="image">
                                    <h2 class="mt-2 mb-2">Isi Lengkap Data Signature QR</h2>
                                @endif

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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            if ($("#tgl").length) {
                $('#tgl').daterangepicker({
                    locale: {format: 'DD/MM/YYYY'},
                    singleDatePicker: true,
                });
            }
        }

        {{--$('.cari').select2({--}}
        {{--    placeholder: 'Pilih Nomor Surat',--}}
        {{--    ajax: {--}}
        {{--        url: '{{url('dashboard/signature-qr/select2-surat-keluar')}}',--}}
        {{--        dataType: 'json',--}}
        {{--        delay: 250,--}}
        {{--        processResults: function (data) {--}}
        {{--            return {--}}
        {{--                results: $.map(data, function (item) {--}}
        {{--                    return {--}}
        {{--                        text: item.no_surat,--}}
        {{--                        id: item.no_surat--}}
        {{--                    }--}}
        {{--                })--}}
        {{--            };--}}
        {{--        },--}}
        {{--        cache: true--}}
        {{--    }--}}
        {{--});--}}

        $('#nomor_surat').keyup(function () {
            var query = $(this).val();
            if (query != '') {
                $.ajax({
                    url: "{{ url('dashboard/signature-qr/fetch-surat-keluar') }}",
                    method: "GET",
                    data: {query: query},
                    success: function (data) {
                        $('#noList').fadeIn();
                        $('#noList').html(data);
                    }
                });
            }
        });

        $(document).on('click', 'li', function () {
            $('#nomor_surat').val($(this).text());
            $('#noList').fadeOut();
        });


        $(".select_cari").select2();

    </script>
@endpush
