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
            <h1>Data PNS Jabatan Pelaksana</h1>
            @if(in_array(Auth::user()->level, ['admin', 'superadmin','sespri']))
                <div class="section-header-button">
                    <a href="javascript:add()"
                       class="btn btn-primary btn-sm"><i
                            class="fa fa-plus mr-50"></i>
                        Tambah Pegawai
                    </a>
                </div>
            @endif
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

    @if(in_array(Auth::user()->level, ['admin', 'superadmin','sespri']))
        <!-- Modal -->
        <div
            class="modal fade"
            id="modal_form"
            {{--        tabindex="-1"--}}
            role="dialog"
            aria-labelledby="exampleModalScrollableTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <form class="form form-horizontal" id="form" name="form" method="post"
                          enctype="multipart/form-data" action="javascript:save();">
                        <div class="modal-header bg-dark text-white" style="padding-top: 10px; padding-bottom: 10px">
                            <h6 class="modal-title" id="judul">Modal title</h6>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body" style="max-height: 400px;">

                            <div class="row">


                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Instansi</label>
                                        <input type="hidden" name="pns_opd" id="pns_opd"
                                               value="{{$dataMaster->opd_id}}">
                                        <input type="text" name="nama_opd" id="nama_opd" readonly
                                               value="{{$dataMaster->opd->nama_opd}}"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Nomor Dokumen</label>
                                        <input type="hidden" name="dokumen_id" id="dokumen_id"
                                               value="{{$dataMaster->id}}">
                                        <input type="text" readonly value="{{$dataMaster->nomor_dokumen}}"
                                               name="dokumen_nomor" id="dokumen_nomor"
                                               class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Pilih Pegawai</label>
                                        <select class="select_cari form-control" id="select_pegawai"
                                                name="select_pegawai">
                                        </select>
                                    </div>
                                    <input type="hidden" name="id_peg" id="id_peg">
                                    <input type="hidden" name="nama" id="nama">
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>NIP</label>
                                        <input type="text" readonly value="" name="nip" id="nip"
                                               class="form-control">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Tanggal Lahir</label>
                                        <input type="text" readonly value="" name="tgl_lahir" id="tgl_lahir"
                                               class="form-control">
                                        <div class="invalid-feedback" id="error_tgl_lahir">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Pendidikan</label>
                                        <input type="text" readonly value="" name="pendidikan" id="pendidikan"
                                               class="form-control">
                                        <div class="invalid-feedback" id="error_pendidikan">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Pangkat/Golongan</label>
                                        <input type="text" readonly value="" name="pangkat_gol" id="pangkat_gol"
                                               class="form-control">
                                        <div class="invalid-feedback" id="error_pangkat_gol">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Jabatan Lama</label>
                                        <input type="text" value="" name="jabatan_lama" id="jabatan_lama"
                                               class="form-control">
                                        <div class="invalid-feedback" id="error_jabatan_lama">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Kelas Lama</label>
                                        <input type="hidden" name="kelas_lama" id="kelas_lama">
                                        <input type="text" readonly value="" name="kelas_lama_format"
                                               id="kelas_lama_format"
                                               class="form-control">
                                        <div class="invalid-feedback" id="error_kelas_lama">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Unker Lama</label>
                                        <input type="text" value="" name="unker_lama" id="unker_lama"
                                               class="form-control">
                                        <div class="invalid-feedback" id="error_unker_lama">
                                        </div>
                                    </div>
                                </div>

                                {{--                                <div class="col-sm-6">--}}
                                {{--                                    <div class="form-group">--}}
                                {{--                                        <label>Jabatan Baru</label>--}}
                                {{--                                        <input type="text" value="" name="jabatan_baru" id="jabatan_baru"--}}
                                {{--                                               class="form-control">--}}
                                {{--                                        <div class="invalid-feedback" id="error_jabatan_baru">--}}
                                {{--                                        </div>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Jabatan Baru</label>
                                        <select class="jabatanbaru form-control" id="jabatan_baru"
                                                name="jabatan_baru">
                                        </select>
                                    </div>
                                </div>


                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Kelas Baru</label>
                                        <input type="number" value="" name="kelas_baru" id="kelas_baru"
                                               class="form-control">
                                        <div class="invalid-feedback" id="error_kelas_baru">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Unker Baru</label>
                                        <select class="select_cari form-control" id="unker_baru"
                                                name="unker_baru">
                                            @foreach($unkerbaru as $x)
                                                <option value="{{$x->KUnKer.'|'.$x->NUnKer}}">
                                                    {{$x->NUnKer}}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="error_unker_baru">
                                        </div>
                                    </div>
                                </div>

{{--                                <div class="col-sm-8">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label>Unker Baru</label>--}}
{{--                                        <input type="text" value="" name="unker_baru" id="unker_baru"--}}
{{--                                               class="form-control">--}}
{{--                                        <div class="invalid-feedback" id="error_unker_baru">--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Urut Pegawai</label>
                                        <input type="number" value="" name="urut_peg" id="urut_peg"
                                               class="form-control">
                                        <div class="invalid-feedback" id="error_urut_peg">
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="modal-footer bg-whitesmoke">
                            <button type="submit" id="btnsave"
                                    class="btn btn-primary mr-1 waves-effect waves-float waves-light">
                                <i class="fa fa-save"></i> <span id="teksSimpan"> Submit</span>
                            </button>
                            <button type="button" id="btnbatal" onclick="add()"
                                    class="btn btn-outline-secondary waves-effect"
                                    style="display: none">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
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
            listPegawaiPelaksana();
            $('#modal_form').on('shown.bs.modal', function () {
                $('#select_pegawai').focus()
            });
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
                    url: "{{ url('dashboard/pns/data-pelaksana/'.$dataMaster->id) }}",
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
                    @if(in_array(Auth::user()->level, ['admin', 'superadmin','sespri']))
                    initClick();
                    @endif
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
                @if(in_array(Auth::user()->level, ['admin', 'superadmin','sespri']))
                initClick();
                @endif
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


        $('#select_pegawai').change(function () {
            // $(this).find(':selected').attr('data-id')
            if ($(this).find(':selected').val() != null) {
                ID_Peg = $(this).find(':selected').attr('data-ID_Peg');
                NIP_Baru = $(this).find(':selected').attr('data-NIP_Baru');
                Nama = $(this).find(':selected').attr('data-Nama');
                Unit_Kerja = $(this).find(':selected').attr('data-Unit_Kerja');
                NJab = $(this).find(':selected').attr('data-NJab');
                NUnKer = $(this).find(':selected').attr('data-NUnKer');
                NGolRu = $(this).find(':selected').attr('data-NGolRu');
                Pangkat = $(this).find(':selected').attr('data-Pangkat');
                Tgl_Lahir = $(this).find(':selected').attr('data-Tgl_Lahir');
                Pendidikan = $(this).find(':selected').attr('data-Pendidikan');
                Jurusan = $(this).find(':selected').attr('data-Jurusan');
                NKelasJab = $(this).find(':selected').attr('data-NKelasJab');
                NKelasIndo = $(this).find(':selected').attr('data-NKelasIndo');

                $('[name="nip"]').val(NIP_Baru);
                $('[name="nama"]').val(Nama);
                $('[name="tgl_lahir"]').val(Tgl_Lahir);
                $('[name="pendidikan"]').val(Pendidikan);
                $('[name="pangkat_gol"]').val(Pangkat + ' (' + NGolRu + ')');
                $('[name="jabatan_lama"]').val(NJab);
                $('[name="kelas_lama"]').val(NKelasJab);
                $('[name="kelas_lama_format"]').val(NKelasJab + ' (' + NKelasIndo + ')');
                $('[name="unker_lama"]').val(NUnKer);
            }

            //alert(NIP_Baru);
        });

        function add() {
            $('#modal_form').modal();
            $('#modal_form').appendTo("body");
            $('#modal_form').modal('show'); // show bootstrap modal
            save_method = 'add';
            nip = '';
            $('#form')[0].reset(); // reset form on modals
            $('.form-control').removeClass('is-invalid'); // clear error class
            $('.invalid-feedback').empty(); // clear error string
            $('#judul').text('FORM TAMBAH PNS KE DALAM JABATAN PELAKSANA'); // Set Title to Bootstrap modal title
            $('#teksSimpan').text('Tambah');
            $('#btnsave').show();
            // pns_opd
            // nama_opd
            // dokumen_id
            // dokumen_nomor
            // nama
            // nip
            // tgl_lahir
            // pendidikan
            // pangkat_gol
            // jabatan_lama
            // kelas_lama
            // unker_lama
            // jabatan_baru
            // kelas_baru
            // unker_baru
            $('[name="pns_opd"]').prop('disabled', false);
            $('[name="nama_opd"]').prop('disabled', false);
            $('[name="dokumen_id"]').prop('disabled', false);
            $('[name="dokumen_nomor"]').prop('disabled', false);
            $('[name="nama"]').prop('disabled', false);
            $('[name="nip"]').prop('disabled', false);
            $('[name="tgl_lahir"]').prop('disabled', false);
            $('[name="pendidikan"]').prop('disabled', false);
            $('[name="pangkat_gol"]').prop('disabled', false);
            $('[name="jabatan_lama"]').prop('disabled', false);
            $('[name="kelas_lama"]').prop('disabled', false);
            $('[name="unker_lama"]').prop('disabled', false);
            $('[name="jabatan_baru"]').prop('disabled', false);
            $('[name="kelas_baru"]').prop('disabled', false);
            $('[name="unker_baru"]').prop('disabled', false);
            $('[name="pns_opd"]').val('{{$dataMaster->opd_id}}');
            listPegawaiPelaksana(save_method, nip);
            $('#btnbatal').hide();
        }

        function initClick() {
            $(".clickable-edit").click(function () {
                save_method = 'update';
                id_peg = $(this).attr('data-id_peg');
                //alert(id);
                nip = $(this).attr('data-nip');
                nama = $(this).attr('data-nama');
                pendidikan = $(this).attr('data-pendidikan');
                tgl_lahir = $(this).attr('data-tgl_lahir');
                pangkat_gol = $(this).attr('data-pangkat_gol');
                jabatan_lama = $(this).attr('data-jabatan_lama');
                kelas_lama = $(this).attr('data-kelas_lama');
                kelas_lama_format = $(this).attr('data-kelas_lama_format');
                unker_lama = $(this).attr('data-unker_lama');
                jabatan_baru = $(this).attr('data-jabatan_baru');
                kelas_baru = $(this).attr('data-kelas_baru');
                KJab = $(this).attr('data-KJab');
                unker_baru = $(this).attr('data-unker_baru');
                K_Unker_SBag = $(this).attr('data-K_Unker_SBag');
                pns_opd = $(this).attr('data-pns_opd');
                dokumen_id = $(this).attr('data-dokumen_id');
                urut_peg = $(this).attr('data-urut_peg');
                $('#form')[0].reset(); // reset form on modals
                $('.form-control').removeClass('is-invalid'); // clear error class
                $('.invalid-feedback').empty(); // clear error string
                $('#modal_form').modal();
                $('#modal_form').appendTo("body");
                $('#modal_form').modal('show'); // sh
                //alert(pthl_id);
                $('[name="id_peg"]').val(id_peg);
                $('[name="nip"]').val(nip);
                $('[name="nama"]').val(nama);
                $('[name="pendidikan"]').val(pendidikan);
                $('[name="tgl_lahir"]').val(tgl_lahir);
                $('[name="pangkat_gol"]').val(pangkat_gol);
                $('[name="jabatan_lama"]').val(jabatan_lama);
                $('[name="kelas_lama"]').val(kelas_lama);
                $('[name="kelas_lama_format"]').val(kelas_lama_format);
                $('[name="unker_lama"]').val(unker_lama);
                //$('[name="jabatan_baru"]').val(KJab+'|'+jabatan_baru).trigger('change');
                //$('[name="jabatan_baru"]').val(jabatan_baru).trigger('change');
                selectedJabatanBaru = '<option value="'+KJab+'|'+jabatan_baru+'" selected>'+jabatan_baru+'</option>';
                $('select[name="jabatan_baru"]').html(selectedJabatanBaru);
                $('[name="kelas_baru"]').val(kelas_baru);
                $('[name="unker_baru"]').val(K_Unker_SBag+'|'+unker_baru).trigger('change');
                $('[name="pns_opd"]').val(pns_opd);
                $('[name="dokumen_id"]').val(dokumen_id);
                $('[name="urut_peg"]').val(urut_peg);

                $('#judul').text('FORM UBAH DATA PNS KE DALAM JABATAN PELAKSANA'); // Set Title to Bootstrap modal titlep modal title
                $('#teksSimpan').text('Simpan Perubahan');
                //$('#dokumen_file_text').text(dokumen_file);
                $('#btnsave').show();
                $('[name="pns_opd"]').prop('disabled', false);
                $('[name="nama_opd"]').prop('disabled', false);
                $('[name="dokumen_id"]').prop('disabled', false);
                $('[name="dokumen_nomor"]').prop('disabled', false);
                $('[name="nama"]').prop('disabled', false);
                $('[name="nip"]').prop('disabled', false);
                $('[name="tgl_lahir"]').prop('disabled', false);
                $('[name="pendidikan"]').prop('disabled', false);
                $('[name="pangkat_gol"]').prop('disabled', false);
                $('[name="jabatan_lama"]').prop('disabled', false);
                $('[name="kelas_lama"]').prop('disabled', false);
                $('[name="unker_lama"]').prop('disabled', false);
                $('[name="jabatan_baru"]').prop('disabled', false);
                $('[name="kelas_baru"]').prop('disabled', false);
                $('[name="unker_baru"]').prop('disabled', false);
                listPegawaiPelaksana(save_method, nip);
                $('#btnbatal').show();
                $('#btnbatal').text('Batal Ubah');
            });
        }

        function save() {
            var url;
            var _method;
            var id;
            var formData = new FormData($('#form')[0]);
            if (save_method == 'add') {
                id = '';
                url = "{{ url('dashboard/pns/create-pegawai-pelaksana/') }}";
                _method = "POST";
            } else {
                id = $('[name="id_peg"]').val();
                url = '{{ url('dashboard/pns/update-pegawai-pelaksana/') }}' + '/' + id;
                _method = "PUT";
                formData.append('_method', 'PUT');
            }

            var token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                url: url,
                type: 'POST',
                data: formData,
                dataType: "JSON",
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.status) //if success close modal and reload ajax table
                    {
                        if (save_method == 'add') {
                            reloadTable();
                            iziToast.success({
                                title: 'Sukses',
                                message: 'Berhasil Input Data',
                                position: 'topRight'
                            });
                            $('#modal_form').modal('hide');
                        } else {
                            reloadTable();
                            iziToast.success({
                                title: 'Sukses',
                                message: 'Berhasil Ubah Data',
                                position: 'topRight'
                            });
                            $('#modal_form').modal('hide');
                        }
                    } else {
                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').addClass('is-invalid'); //select parent twice to
                            $('#error_' + data.inputerror[i] + '').text(data.error_string[i]);
                            $('[name="' + data.inputerror[i] + '"]').focus();
                        }
                    }
                },
                error: function (xhr) {
                    iziToast.error({
                        title: 'Error',
                        message: xhr.responseText,
                        position: 'topRight'
                    });
                }
            });
        }

        if (jQuery().daterangepicker) {
            if ($("#tgl_surat").length) {
                $('#tgl_surat').daterangepicker({
                    locale: {format: 'DD/MM/YYYY'},
                    singleDatePicker: true,
                });
            }
        }

        function deleteData(paramId) {
            var url = '{{ url('dashboard/pns/delete-pegawai-pelaksana/') }}';
            deleteDataTable(paramId, url);
        }

        function listPegawaiPelaksana(save_method, nip) {
            $.ajax({
                url: '{{ url('dashboard/pns/get-list-pelaksana/'.$dataMaster->opd_id) }}',
                type: "GET",
                data:
                    {
                        mode: save_method,
                        nip: nip,
                    },
                success: function (res) {
                    $('select[name="select_pegawai"]').html(res);
                },
                error(res) {
                    console.log(res);
                }
            });
        }

        $('.jabatanbaru').select2({
            //minimumInputLength: 3,
            allowClear: true,
            placeholder: 'masukkan jabatan baru',
            ajax: {
                url: '{{url('dashboard/pns/select-jabatan-baru')}}',
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.NJab,
                                id: item.KJab + '|' + item.NJab
                            }
                        })
                    };
                },
                cache: true
            }
        });


    </script>
@endpush
