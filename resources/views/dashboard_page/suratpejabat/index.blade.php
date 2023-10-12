@extends('mylayouts.app')
@section('title', 'Surat Masuk Intern')
@push('vendor-css')
    <!--begin::Page Vendors Styles(used by this page)-->
    <link rel="stylesheet" href="{{ assetku('magnific-popup/magnific-popup.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets//modules/bootstrap-daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Buttons/css/buttons.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/datatables.min.css')}}">
    <link rel="stylesheet"
          href="{{assetku('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
    <style>
        table.dataTable tbody td {
            vertical-align: middle;
        }
    </style>
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Daftar Surat Masuk Intern</h1>
            @if(in_array(Auth::user()->level, ['superadmin', 'admin', 'sespri']))
                <div class="section-header-button">
                    <a href="{{ route('surat-masuk-pejabat.form') }}"
                       class="btn btn-primary btn-sm"><i
                            class="fa fa-plus mr-50"></i>
                        Tambah
                    </a>
                </div>
            @endif
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item">Daftar Surat Masuk Intern</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        Surat Masuk Intern adalah surat masuk yang diserahkan secara langsung
                                        kepada pejabat
                                        tinggi (Bupati, Wakil Bupati, Sekda, asisten, kepala perangkat daerah) saat pengirim
                                        surat bertamu
                                        atau bertemu langsung dengan pejabat, dan surat tersebut tidak didisposisikan ke
                                        perangkat
                                        daerah atau pejabat tinggi lainnya.
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
                            <table class="table table-striped table-bordered table-hover" id="hideyori_datatable">
                                <thead>
                                <tr>
                                    @if(in_array(Auth::user()->level, ['superadmin', 'admin', 'sespri']))
                                        <th><input type="checkbox" id="check-all"></th>
                                    @endif
                                    <th>No.</th>
                                    <th>NO SURAT</th>
                                    <th>TANGGAL SURAT</th>
                                    <th>DARI (PENGIRIM)</th>
                                    <th>HAL</th>
                                    <th>QR</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        @if(in_array(Auth::user()->level, ['sespri', 'admin', 'superadmin']))
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-lg-6  col-xl-6 col-6 text-left" id="div_opsi"
                                         style="display: none">
                                        <div class="dropdown d-inline">
                                            <button class="btn btn-dark dropdown-toggle" type="button"
                                                    id="dropdownMenuButton2"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Pilih Opsi
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item has-icon" href="javascript:bulkDelete()"><i
                                                        class="fa fa-trash text-danger"></i> Hapus</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6  col-xl-6 col-6 text-right" id="div_ekspor"
                                         style="display: none">
                                    </div>
                                </div>
                            </div>
                        @endif
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
    <!--begin::Page Scripts(used by this page)-->
    <script src="{{assetku('assets/modules/datatables/datatables.min.js')}}"></script>
    <script
        src="{{assetku('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{assetku('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js')}}"></script>
    @include('components.buttonDatatables')
    <!--begin::Page Scripts(used by this page)-->
    <script src="{{ assetku('assets/jshideyorix/mydatatable.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/deletertable.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            @if(session('pesan_status'))
            tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
            @endif
        });

        $(document).ready(function () {
            table = $('#hideyori_datatable').DataTable({
                aLengthMenu: [
                    [25, 50, 100, -1],
                    [25, 50, 100, "All"]
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
                    url: "{{ route('surat-masuk-pejabat.data') }}",
                    type: "GET",
                    data: function (d) {
                        d.tgl_mulai = $("#tgl_mulai").val();
                        d.tgl_akhir = $("#tgl_akhir").val();
                    }
                },
                @if(in_array(Auth::user()->level, ['superadmin', 'admin', 'sespri']))
                order: [[7, "desc"]],
                @else
                order: [[6, "desc"]],
                @endif
                columns: [
                        @if(in_array(Auth::user()->level, ['superadmin', 'admin', 'sespri']))
                    {
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                        @endif
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {data: 'no_surat', name: 'no_surat', responsivePriority: -1},
                    {data: 'tgl_surat', name: 'tgl_surat'},
                    {data: 'dari', name: 'dari'},
                    {data: 'perihal', name: 'perihal'},
                    {data: 'qrcode', name: 'qrcode', orderable: false, searchable: false, className: 'text-center'},
                    {
                        data: 'action', name: 'id', searchable: false, className: 'text-center'
                    },
                ],

                rowCallback: function (row, data, index) {
                    cellValue = data['id'];
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
                        @if(Auth::user()->level != 'user')
                            exporni = '1,2,3,4';
                        @else
                            exporni = '0,1,2,3';
                        @endif

                        var buttons_dom = new $.fn.dataTable.Buttons(table, {
                            buttons: [
                                {
                                    extend: 'print',
                                    text: 'Print',
                                    title: 'Surat Masuk Intern',
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
                                    title: 'Surat Masuk Intern',
                                    exportOptions: {
                                        //columns: ':visible'
                                        columns: exporni
                                    }
                                },
                                {
                                    extend: 'excelHtml5',
                                    text: 'Excel',
                                    title: 'Surat Masuk Intern',
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

        @if(in_array(Auth::user()->level, ['superadmin', 'admin', 'sespri']))

        function deleteData(paramId) {
            var url = '{{ url('dashboard/surat-masuk-pejabat/delete/') }}';
            deleteDataTable(paramId, url);
        }

        function bulkDelete() {
            var url = '{{ url('dashboard/surat-masuk-pejabat/bulkDelete/') }}';
            bulkDeleteTable(url);
        }

        @endif


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
