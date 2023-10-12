@extends('mylayouts.front')
@section('title', $sifat_surat == 'biasa' ? 'Detail Surat Masuk' : 'Detail Surat '.ucwords($sifat_surat))
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
                            <h4>Data Surat {{$sifat_surat == 'biasa' ? 'Masuk' : ucwords($sifat_surat)}}</h4>
                            <div class="card-header-action">
                                <a data-collapse="#mycard-collapse" class="btn btn-sm btn-icon btn-info" href="#"><i
                                        class="fas fa-plus"></i></a>
                            </div>
                        </div>
                        <div class="collapse" id="mycard-collapse">
                            <div class="card-body">
                                @if($kode!='')
                                    <div class="col-sm-12">
                                        <div class="row mb-3">
                                            <label class="col-sm-5 col-lg-5 col-form-label">Kode</label>

                                            <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                                {{$kode}}
                                            </label>
                                        </div>
                                    </div>
                                    <hr class="mb-2">
                                @endif
                                @if($indek!='')
                                    <div class="col-sm-12">
                                        <div class="row mb-3">
                                            <label class="col-sm-5 col-lg-5 col-form-label">Nomor Agenda</label>

                                            <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                                {{$indek}}
                                            </label>
                                        </div>
                                    </div>
                                    <hr class="mb-2">
                                @endif
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
                                @if($sifat_surat=='langsung')
                                    <hr class="mb-2">
                                    <div class="col-sm-12">
                                        <div class="row mb-3">
                                            <label class="col-sm-5 col-lg-5 col-form-label">Catatan Perjalanan Surat</label>

                                            <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                                {{$catatan}}
                                            </label>
                                        </div>
                                    </div>
                                @endif
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
                @if(count($listDetail)>0)

                    <div class="col-sm-12 order-sm-0 order-lg-1 order-xl-1">


                        <div class="alert alert-light">
                            <div class="alert-body">
                                Menerima Surat {{$sifat_surat == 'biasa' ? 'Masuk' : ucwords($sifat_surat)}} ini, klik
                                tombol <a href="{{url('dashboard/disposisi/' . $id)}}" class="btn btn-sm btn-dark"
                                          target="_blank"><i class="fas fa-edit"></i> Form Penerimaan Surat</a>
                            </div>
                        </div>
                        <div class="activities">
                            @foreach($listDetail as $data)

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

                                @if($input_tgl!=null)
                                    @include('dashboard_page.suratmasuk.tanggalisiuser')
                                @else
                                    @include('dashboard_page.suratmasuk.tanggalkosonguser')
                                @endif

                            @endforeach
                        </div>
                    </div>
                @endif
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
