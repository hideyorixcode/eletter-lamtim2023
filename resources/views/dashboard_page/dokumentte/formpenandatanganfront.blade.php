@extends('mylayouts.front')
@section('title', 'Form Tanda Tangan Dokumen TTE ')
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
            <div class="row">
                <div class="col-sm-4 order-sm-0 order-lg-1 order-xl-1">
                    <div class="card">
                        <div class="card-body">
                            <div class="empty-state">
                                @if($berkas)
                                    <a href="{{url('berkas/temp/'.$berkas)}}" target="_blank">
                                        <img class="img-fluid" style="height: 75px"
                                             src="{{url('uploads/pdf_icon.png')}}"
                                             alt="image">
                                        <h2 class="mt-2 mb-2">Cek Berkas</h2>
                                    </a>
                                @else
                                    <img class="img-fluid"
                                         src="{{url('kodeqr/'.$qrcode)}}"
                                         alt="{{$no_dokumen}}">
                                    <h2 class="mt-2 mb-2">Dokumen TTE </h2>
                                @endif
                            </div>
                            <form id="form" name="form" role="form" action="{{$action}}"
                                  enctype="multipart/form-data" method="post" autocomplete="off">
                                {{csrf_field()}}

                                <div class="form-group d-none">
                                    <label class="d-none">NIK</label>
                                    <input type="text" class="form-control d-none" name="id" id="id"
                                           value="{{$id}}" readonly>
                                    <input type="text" class="form-control d-none" name="nik" id="nik"
                                           value="{{$jenis_ttd->nik}}" readonly>
                                    @error('nik')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror

                                </div>

                                <div class="form-group">
                                    <label>Passphrase</label>
                                    <input type="password"
                                           class="form-control @error('passphrase') is-invalid @enderror"
                                           name="passphrase" id="passphrase" value="" autocomplete="off">
                                    @error('passphrase')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror

                                </div>

                                <button type="submit" class="btn btn-primary">Tanda Tangan Dokumen</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8 order-sm-0 order-lg-0 order-xl-0">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">1. No Dokumen</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$no_dokumen}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">2. Tgl Dokumen</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$tgl_dokumen}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">3. Lampiran</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$lampiran}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">4. Hal</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$perihal}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">7. Ditandangani Oleh</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$jenis_ttd->jenis_ttd}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
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
            if ($("#tgl_dokumen").length) {
                $('#tgl_dokumen').daterangepicker({
                    locale: {format: 'DD/MM/YYYY'},
                    singleDatePicker: true,
                });
            }
        }
    </script>
@endpush
