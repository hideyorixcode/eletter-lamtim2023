@extends('mylayouts.app')
@section('title', 'Kode QR')
@push('vendor-css')
    <!--begin::Page Vendors Styles(used by this page)-->
    <link rel="stylesheet" href="{{ assetku('magnific-popup/magnific-popup.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets//modules/bootstrap-daterangepicker/daterangepicker.css')}}">
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Daftar Kode QR</h1>
            <div class="section-header-button">
                <a href="{{ route('signature-qr.form') }}"
                   class="btn btn-primary btn-sm"><i
                        class="fa fa-plus mr-50"></i>
                    Tambah
                </a>
            </div>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item">Daftar Kode QR</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-row mb-3">
                                        <div class="col-lg-12">
                                            <label>Dari Perangkat Daerah :</label>
                                            <select class="select_cari form-control" id="id_opd_fk"
                                                    name="id_opd_fk">
                                                <option value="">Seluruh</option>
                                                @foreach($listPerangkat as $nama => $value)
                                                    <option
                                                        value={{$value}}>{{$nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-row mb-3">
                                        <div class="col-lg-12">
                                            <label>Dari Tanggal :</label>
                                            <input class="form-control datepickerindo" placeholder=""
                                                   name="tgl_mulai"
                                                   id="tgl_mulai"
                                                   type="text">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-3">
                                    <div class="form-row mb-3">
                                        <div class="col-lg-12">
                                            <label>Sampai Tanggal :</label>
                                            <input class="form-control datepickerindo" placeholder=""
                                                   name="tgl_akhir" id="tgl_akhir" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 text-right">
                                    <button class="btn btn-outline-info btn-sm" onclick="getViewData(1)"
                                            type="button"
                                            id="btnubah">
                                        <i class="fa fa-undo"></i> Saring Data
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-md-12">
                    <div class="alert alert-warning">
                        Klik pada Gambar QRCode untuk memperbesar
                    </div>
                </div>
                <div class="col-md-12">
                    @include('components.loader')
                    <div id="renderviewData">

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script src="{{assetku('assets/modules/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{assetku('assets/modules/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/deleterview.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/myrenderview.js')}}"></script>
    <script src="{{ assetku('magnific-popup/jquery.magnific-popup.min.js')}}"></script>
    <script src="{{ assetku('assets/modules/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <!--begin::Page Scripts(used by this page)-->
    <script type="text/javascript">
        $(document).ready(function() {
            getViewData(1);
            @if(session('pesan_status'))
            tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
            @endif
        });

        if (jQuery().daterangepicker) {
            // if ($("#tgl_mulai").length) {
            //     $('#tgl_mulai').daterangepicker({
            //         locale: {format: 'DD/MM/YYYY'},
            //         singleDatePicker: true,
            //     });
            // }
            // if ($("#tgl_akhir").length) {
            //     $('#tgl_akhir').daterangepicker({
            //         locale: {format: 'DD/MM/YYYY'},
            //         singleDatePicker: true,
            //     });
            // }

            var fromDate = new Date();
            $("#tgl_mulai").daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    autoApply: true,
                    locale: {
                        format: 'DD/MM/YYYY'
                    },
                },
                function (start, end, label) {
                    fromDate = start.format('DD/MM/YYYY');
                    $("#tgl_akhir").daterangepicker({
                        singleDatePicker: true,
                        showDropdowns: true,
                        autoApply: true,
                        locale: {
                            format: 'DD/MM/YYYY'
                        },
                        minDate: fromDate
                    });
                });
        }

        $(document).on('click', '.pagination a', function (event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            getViewData(page);
        });

        function getViewData(page) {
            var pagenya = page ? page : 1;
            $(".loaderData").show();
            var urlData = "{{ url('dashboard/signature-qr/data') }}";
            $.ajax({
                url: urlData,
                type: "GET",
                data:
                    {
                        page: pagenya,
                        id_opd_fk: $("#id_opd_fk").val(),
                        tgl_mulai: $("#tgl_mulai").val(),
                        tgl_akhir: $("#tgl_akhir").val(),
                        cari: $("#textSearch").val(),
                        arrayList: arrayList,
                        page_count: $("#page_count").val(),
                    },
                success: function (data) {
                    $('#renderviewData').html(data);
                    $(".loaderData").hide();
                    initMagnific();
                    checkall();
                    initCountpage();
                }
            });
        }

        function deleteData(paramId) {
            var url = '{{ url('dashboard/signature-qr/delete/') }}';
            deleteDataView(paramId, url);
        }

        function bulkDelete() {
            var url = '{{ url('dashboard/signature-qr/bulkDelete/') }}';
            bulkDeleteView(url);
        }


        function initMagnific() {
            $('.image-popup-no-margins').magnificPopup({
                type: 'image',
                closeOnContentClick: true,
                fixedContentPos: true,
                mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
                image: {
                    verticalFit: true
                },
            });
        }
    </script>
    <!--end::Page Scripts-->
@endpush
