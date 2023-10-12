@extends('mylayouts.front')
@section('title', 'Detail Surat Masuk Intern')
@push('vendor-css')
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/datatables.min.css')}}">
    <link rel="stylesheet"
          href="{{assetku('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/bootstrap-daterangepicker/daterangepicker.css')}}">
    <style>
        td {
            white-space: normal !important;
            word-wrap: break-word;
        }

        table {
            table-layout: fixed;
        }
    </style>
@endpush
@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-sm-12 order-sm-0 order-lg-0 order-xl-0">
                    <div class="card">
                        <div class="card-header">
                            <h4>Data Surat Masuk Intern</h4>

                        </div>

                            <div class="card-body">

                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-5 col-lg-5 col-form-label">No Surat</label>

                                        <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                            {{$no_surat}}
                                        </label>
                                    </div>
                                </div>
                                <hr class="mb-2">
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-5 col-lg-5 col-form-label">Tgl Surat</label>

                                        <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                            {{$tgl_surat}}
                                        </label>
                                    </div>
                                </div>

                                <hr class="mb-2">
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-5 col-lg-5 col-form-label">Lampiran</label>

                                        <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                            {{$lampiran}}
                                        </label>
                                    </div>
                                </div>

                                <hr class="mb-2">
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-5 col-lg-5 col-form-label">Dari</label>

                                        <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                            {{$dari}}
                                        </label>
                                    </div>
                                </div>
                                <hr class="mb-2">
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-5 col-lg-5 col-form-label">Kepada</label>

                                        <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                            {{$kepada}}
                                        </label>
                                    </div>
                                </div>
                                <hr class="mb-2">
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-5 col-lg-5 col-form-label">Hal</label>

                                        <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                            {{$perihal}}
                                        </label>
                                    </div>
                                </div>

                                <hr class="mb-2">
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-5 col-lg-5 col-form-label">Catatan</label>

                                        <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                            {{$catatan}}
                                        </label>
                                    </div>
                                </div>

                                @if($berkas)
                                    <hr class="mb-2">
                                    <div class="col-sm-12">
                                        <div class="row mb-3">
                                            <label class="col-sm-5 col-lg-5 col-form-label">Berkas</label>

                                            <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                                <a href="{{url('berkas/'.$berkas)}}" target="_blank">
                                                    <img class="img-fluid" style="height: 75px"
                                                         src="{{url('uploads/pdf_icon.png')}}"
                                                         alt="image">
                                                    <h2 class="mt-2 mb-2">Download Berkas</h2>
                                                </a>
                                            </label>
                                        </div>
                                    </div>
                                @endif
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
    <!--end::Page Vendors-->
    <!--begin::Page Scripts(used by this page)-->
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>

    <script src="{{assetku('assets/modules/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script>

    </script>
@endpush
