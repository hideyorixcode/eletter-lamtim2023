@extends('mylayouts.app')
@section('title', 'Pimpinan / Instansi')
@push('vendor-css')
    <!--begin::Page Vendors Styles(used by this page)-->
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/datatables.min.css')}}">
    <link rel="stylesheet"
          href="{{assetku('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Buttons/css/buttons.bootstrap4.min.css')}}">
    <!--end::Page Vendors Styles-->
@endpush
@push('library-css')
@endpush
@section('content')
    <!--begin::Card-->

    <section class="section">
        <div class="section-header">
            <h1>Daftar Pimpinan / Instansi</h1>
            <div class="section-header-button">
                <a href="{{route('perangkat-daerah.form')}}"
                   class="btn btn-primary btn-sm"><i
                        class="fa fa-plus mr-50"></i>
                    Tambah
                </a>
            </div>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item">Daftar Pimpinan / Instansi</div>
            </div>
        </div>

        <div class="section-body">


            <div class="card">
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="hideyori_datatable">
                        <thead>
                        <tr>
                            <th width="2%"><input type="checkbox" id="check-all"></th>
                            <th width="3%">No.</th>
                            <th width="55%">Pimpinan / Instansi</th>
                            <th width="15%">Status</th>
                            <th width="15%">Jenis</th>
                            <th width="10%">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                @if(in_array(Auth::user()->level, ['admin', 'superadmin']))
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
    </section>
@endsection

@push('scripts')
    <script src="{{assetku('assets/modules/datatables/datatables.min.js')}}"></script>
    <script
        src="{{assetku('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{assetku('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js')}}"></script>
    <script src="{{assetku('assets/modules/select2/dist/js/select2.full.min.js')}}"></script>
    @include('components.buttonDatatables')
    <script src="{{assetku('assets/modules/jquery-ui/jquery-ui.min.js')}}"></script>
    <!--end::Page Vendors-->
    <!--begin::Page Scripts(used by this page)-->
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/mydatatable.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/deletertable.js')}}"></script>
    <!--end::Page Vendors-->
    <!--begin::Page Scripts(used by this page)-->
    <script type="text/javascript">

        @if(session('pesan_status'))
        tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
        @endif

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
                    url: "{{ route('perangkat-daerah.data') }}",
                    type: "GET",
                },
                order: [[4, "asc"]],
                columns: [
                    {
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {data: 'nama_opd', name: 'nama_opd', responsivePriority: -1},
                    {
                        data: 'active',
                        name: 'active',
                        responsivePriority: -1,
                        className: 'text-center'
                    },
                    {
                        data: 'jenis',
                        name: 'jenis',
                        className: 'text-center',
                        searchable: false,
                    },
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
                ],

                rowCallback: function (row, data, index) {
                    cellValue = data['id_opd'];
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
                            exporni = '1,2,4';
                        @else
                            exporni = '0,1,3';
                        @endif

                        var buttons_dom = new $.fn.dataTable.Buttons(table, {
                            buttons: [
                                {
                                    extend: 'print',
                                    text: 'Print',
                                    title: 'Daftar Pimpinan / Instansi',
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
                                    title: 'Daftar Pimpinan / Instansi',
                                    exportOptions: {
                                        //columns: ':visible'
                                        columns: exporni
                                    }
                                },
                                {
                                    extend: 'excelHtml5',
                                    text: 'Excel',
                                    title: 'Daftar Pimpinan / Instansi',
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
                //initClick();
            });


        });

        function deleteData(paramId) {
            var url = '{{ url('dashboard/perangkat-daerah/delete/') }}';
            deleteDataTable(paramId, url);
        }


        function bulkDelete() {
            var url = '{{ url('dashboard/perangkat-daerah/bulkDelete/') }}';
            bulkDeleteTable(url)
        }
    </script>
@endpush
