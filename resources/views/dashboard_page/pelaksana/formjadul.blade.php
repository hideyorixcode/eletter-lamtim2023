@extends('mylayouts.app')
@section('title', 'Data PNS Jabatan Pelaksana ')
@push('vendor-css')
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/datatables.min.css')}}">
    <link rel="stylesheet"
          href="{{assetku('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Buttons/css/buttons.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets//modules/bootstrap-daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{ assetku('magnific-popup/magnific-popup.min.css')}}">
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

        hr.style5 {
            background-color: #fff;
            border-top: 2px dashed #8c8b8b;
        }
    </style>
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Data Lengkap PNS Jabatan Pelaksana

                <a href="{{url('dashboard/pns/detail-pelaksana/'.$idhash)}}"
                   class="btn btn-success btn-sm"><i
                        class="fa fa-edit mr-50"></i>
                    Tampilan Input Data
                </a>
            </h1>

            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{url('dashboard/pns/jabatan-pelaksana')}}">Dokumen Penomoran </a>
                </div>
                <div class="breadcrumb-item active">Tabel Data</div>
            </div>
        </div>
        <div class="section-body">

            <div class="card">
                <div class="card-body">
                    <table class="table table-striped table-bordered table-responsive" id="hideyori_datatable">
                        <thead class="text-center">
                        <tr>
                            <th rowspan="2">NO</th>
                            <th rowspan="2">NAMA</th>
                            <th rowspan="2">NIP</th>
                            <th rowspan="2">TANGGAL LAHIR</th>
                            <th rowspan="2">PENDIDIKAN</th>
                            <th rowspan="2">PANGKAT (GOL.RUANG)</th>
                            <th colspan="3">LAMA</th>
                            <th colspan="3">BARU</th>
                            <th rowspan="2">OPD</th>
                            <th rowspan="2">QR</th>
                            <th rowspan="2">Actions</th>
                        </tr>
                        <tr>
                            <th>JABATAN</th>
                            <th>KELAS JABATAN</th>
                            <th>UNIT KERJA</th>
                            <th>JABATAN</th>
                            <th>KELAS JABATAN</th>
                            <th>UNIT KERJA</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                @if(in_array(Auth::user()->level, ['admin', 'superadmin','sespri']))
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12  col-xl-12 col-12 text-right" id="div_ekspor"
                                 style="display: none">
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </section>


@endsection
@push('scripts')
    <script src="{{assetku('assets/modules/datatables/datatables.min.js')}}"></script>
    <script
        src="{{assetku('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{assetku('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js')}}"></script>
    <script src="{{assetku('assets/modules/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{assetku('assets/modules/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{ assetku('assets/modules/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <!--begin::Page Scripts(used by this page)-->
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/mydatatable.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/deletertable.js')}}"></script>
    <script src="{{ assetku('magnific-popup/jquery.magnific-popup.min.js')}}"></script>
    @include('components.buttonDatatables')

    <!--begin::Page Scripts(used by this page)-->
    <script type="text/javascript">
        @if(session('pesan_status'))
        tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
        @endif

        $(document).ready(function () {


            table = $('#hideyori_datatable').DataTable({
                aLengthMenu: [
                    [10, 50, 100, -1],
                    [10, 50, 100, "All"]
                ],
                paging: true,
                processing: true,
                serverSide: true,
                // responsive: true,
                autoWidth: false,
                pageLength: -1,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari dan Tekan Enter..."
                },
                {{--ajax: "{{ route('pthl.data') }}",--}}
                ajax: {
                    url: "{{ url('dashboard/pns/data-pelaksana-tabel/'.$dataMaster->id) }}",
                    type: "GET",
                },
                columns: [
                    @if(in_array(Auth::user()->level, ['admin', 'superadmin','sespri']))
                    // {
                    //     data: 'checkbox',
                    //     name: 'checkbox',
                    //     orderable: false,
                    //     searchable: false,
                    //     className: 'text-center',
                    //     responsivePriority: -1
                    // },
                        @endif

                    {
                        data: 'urut_peg', name: 'urut_peg', responsivePriority: -1
                    },
                    {data: 'nama', name: 'nama'},
                    {data: 'nip', name: 'nip'},
                    {data: 'tgl_lahir', name: 'tgl_lahir'},
                    {data: 'pendidikan', name: 'pendidikan'},
                    {data: 'pangkat_gol', name: 'pangkat_gol'},
                    {data: 'jabatan_lama', name: 'jabatan_lama'},
                    {data: 'kelas_lama', name: 'kelas_lama'},
                    {data: 'unker_lama', name: 'unker_lama'},
                    {data: 'jabatan_baru', name: 'jabatan_baru'},
                    {data: 'kelas_baru', name: 'kelas_baru'},
                    {data: 'unker_baru', name: 'unker_baru'},
                    {data: 'pns_opd', name: 'pns_opd'},
                    {
                        data: 'pns_qr',
                        name: 'pns_qr',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },

                    {
                        data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'
                    },

                ],

                rowCallback: function (row, data, index) {
                    cellValue = data['id_peg'];
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
                        @if(in_array(Auth::user()->level, ['admin', 'superadmin']))
                        $('#div_opsi').show();
                        $('#check-all').show();
                        @endif
                        $('#div_ekspor').html('');
                        $('#div_ekspor').show();


                        exporni = '0,1,2,3,4,5,6,7,8,9,10,11,12';


                        var title = 'PNS Jabatan Pelaksana {{$dataMaster->opd->nama_opd.' Nomor SK : '.$dataMaster->nomor_dokumen}}'


                        var buttons_dom = new $.fn.dataTable.Buttons(table, {
                            buttons: [
                                {
                                    extend: 'print',
                                    text: 'Print',
                                    title: title,
                                    orientation: 'landscape',
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
                                    title: title,
                                    exportOptions: {
                                        //columns: ':visible'
                                        columns: exporni
                                    }
                                },
                                {
                                    extend: 'excelHtml5',
                                    text: 'Excel',
                                    title: title,
                                    exportOptions: {
                                        //columns: ':visible'
                                        columns: exporni
                                    }
                                },

                            ]
                        }).container().appendTo($('#div_ekspor'));

                    } else {
                        @if(in_array(Auth::user()->level, ['admin', 'superadmin','sespri']))
                        $('#div_opsi').hide();
                        @endif
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
@endpush
