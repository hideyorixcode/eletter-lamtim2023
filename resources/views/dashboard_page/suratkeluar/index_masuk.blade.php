@extends('mylayouts.app')
@section('title', 'Surat Masuk Instansi')
@push('vendor-css')
    <!--begin::Page Vendors Styles(used by this page)-->
    <link rel="stylesheet" href="{{ assetku('magnific-popup/magnific-popup.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Buttons/css/buttons.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets//modules/bootstrap-daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/datatables.min.css')}}">
    <link rel="stylesheet"
          href="{{assetku('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">

@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Daftar Surat Masuk antar Instansi </h1>

            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item">Daftar Surat Masuk antar Instansi</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">


                <div class="col-md-12">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="alert alert-info">
                                                Surat Masuk antar instansi adalah surat yang masuk dari satu instansi di
                                                lingkungan Pemerintah Provinsi Lampung
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="alert alert-warning">
                                                Klik pada Gambar QRCode untuk memperbesar
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Dari Tanggal :</label>
                                                        <input class="form-control datepickerindo" placeholder=""
                                                               name="tgl_mulai"
                                                               id="tgl_mulai"
                                                               type="text">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Sampai Tanggal :</label>
                                                        <input class="form-control datepickerindo" placeholder=""
                                                               name="tgl_akhir"
                                                               id="tgl_akhir"
                                                               type="text">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 text-right">
                                                    <button class="btn btn-outline-info btn-sm" onclick="reloadTable()"
                                                            type="button">
                                                        <i class="fa fa-undo"></i> Saring Data
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-striped table-bordered" id="hideyori_datatable">
                                        <thead>
                                        <tr>


                                            <th style="width: 5%">No.</th>
                                            <th style="width: 25%">No Surat</th>
                                            <th style="width: 20%">Tanggal Surat</th>
                                            <th class="none">Dari</th>
                                            <th class="none">Kepada</th>
                                            <th class="none">Perihal</th>
                                            <th style="width: 35%">Penandatangan</th>
                                            <th style="width: 5%">QRCODE</th>

                                            <th class="none">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="card-footer">
                                    <div class="row">

                                        <div class="col-lg-12  col-xl-12 col-12 text-right" id="div_ekspor"
                                             style="display: none">
                                        </div>
                                    </div>
                                </div>

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
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
    <script src="{{ assetku('magnific-popup/jquery.magnific-popup.min.js')}}"></script>
    <script src="{{ assetku('assets/modules/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script src="{{assetku('assets/modules/datatables/datatables.min.js')}}"></script>
    <script
        src="{{assetku('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{assetku('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js')}}"></script>
    @include('components.buttonDatatables')
    <!--begin::Page Scripts(used by this page)-->
    <script src="{{ assetku('assets/jshideyorix/mydatatable.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/deletertable.js')}}"></script>
    <!--begin::Page Scripts(used by this page)-->
    <script type="text/javascript">
        $(document).ready(function () {
            @if(session('pesan_status'))
            tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
            @endif
        });

        $(document).ready(function () {
            table = $('#hideyori_datatable').DataTable({
                aLengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                paging: true,
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari dan Tekan Enter..."
                },
                {{--ajax: "{{ route('perangkat-daerah.data') }}",--}}
                ajax: {
                    url: "{{ route('surat-masuk-instansi.data') }}",
                    type: "GET",
                    data: function (d) {
                        d.tgl_mulai = $("#tgl_mulai").val();
                        d.tgl_akhir = $("#tgl_akhir").val();
                    }
                },

                order: [[8, "desc"]],

                columns: [

                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {data: 'no_surat', name: 'no_surat', responsivePriority: -1},
                    {data: 'tgl_surat', name: 'tgl_surat'},
                    {data: 'nama_opd', name: 'tbl_opd.nama_opd'},
                    {data: 'kepada', name: 'kepada'},
                    {data: 'perihal', name: 'perihal'},
                    {data: 'jenis_ttd', name: 'v_penandatangan.jenis_ttd'},

                    {data: 'qrcode', name: 'qrcode', orderable: false, searchable: false, className: 'text-center'},
                    {data: 'action', name: 'id', searchable: false, className: 'text-center'},
                ],

                rowCallback: function (row, data, index) {
                    cellValue = data['id_qr'];
                    // console.log(cellValue);
                    var html = $(row);
                    if (array_data.includes(cellValue, 0)) {
                        var input = html.find('input[type=checkbox]').prop('checked', 'checked')
                    }
                },
                drawCallback: function () {
                    $('.data-check').on('change', function () {
                        console.log($(this).val());
                        if ($(this).is(':checked')) {
                            array_data.push($(this).val())
                        } else {
                            var index = array_data.indexOf($(this).val());
                            if (index !== -1) {
                                array_data.splice(index, 1);
                            }
                        }
                    });
                    var totalData = table.page.info().recordsTotal;
                    if (totalData > 0) {
                        $('#div_opsi').show();
                        $('#div_ekspor').html('');
                        $('#div_ekspor').show();
                        $('#check-all').show();

                            exporni = '0,1,2,3,4,5,6';


                        var buttons_dom = new $.fn.dataTable.Buttons(table, {
                            buttons: [
                                {
                                    extend: 'print',
                                    text: 'Print',
                                    title: 'Surat Masuk antar Instansi',
                                    customize: function (win) {
                                        $(win.document.body).find('h1').css('text-align', 'center');
                                    },
                                    exportOptions: {
                                        columns: exporni
                                        //columns : '0,1,2,3,4,5,6,7,8'
                                    }
                                },
                                {
                                    extend: 'copyHtml5',
                                    text: 'Copy',
                                    title: 'Surat Masuk antar Instansi',
                                    exportOptions: {
                                        //columns: ':visible'
                                        columns: exporni
                                    }
                                },
                                {
                                    extend: 'excelHtml5',
                                    text: 'Excel',
                                    title: 'Surat Masuk antar Instansi',
                                    exportOptions: {
                                        //columns: ':visible'
                                        columns: exporni
                                    }
                                },

                            ]
                        }).container().appendTo($('#div_ekspor'));
                    } else {
                        $('#div_opsi').hide();
                        $('#div_ekspor').hide();
                        $('#check-all').hide();
                    }
                    initMagnific();
                },
                "error": function (xhr, error, thrown) {
                    console.log("Error occurred!");
                    console.log(xhr, error, thrown);
                }
            });

            $('#hideyori_datatable_filter input').unbind();
            $('#hideyori_datatable_filter input').bind('keyup', function (e) {
                if (e.keyCode == 13) {
                    table.search(this.value).draw();
                }
            });

            $('#hideyori_datatable').on('error.dt', function (e, settings, techNote, message) {
                console.log('An error has been reported by DataTables: ', message);
            }).DataTable();

            table.on('responsive-display', function (e, datatable, row, showHide, update) {
                initMagnific();
            });


        });

        if (jQuery().daterangepicker) {

            var fromDate = new Date();
            $("#tgl_mulai").daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    autoApply: true,
                    locale: {
                        format: 'DD/MM/YYYY'
                    },
                    autoUpdateInput: false,
                },
                function (start, end, label) {
                    $("#tgl_mulai").val(start.format('DD/MM/YYYY'));
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
