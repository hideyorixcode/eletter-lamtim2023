@extends('mylayouts.app')
@section('title', 'Visualisasi TTE '.$row->jenis_ttd)
@push('vendor-css')
    <!--begin::Page Vendors Styles(used by this page)-->
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/datatables.min.css')}}">
    <link rel="stylesheet"
          href="{{assetku('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ assetku('magnific-popup/magnific-popup.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Buttons/css/buttons.bootstrap4.min.css')}}">
    <!--end::Page Vendors Styles-->
@endpush
@push('library-css')
@endpush
@section('content')
    <!--begin::Card-->

    <section class="section">
        <div class="section-header">
            <h1>Visualisasi TTE {{$row->jenis_ttd}}</h1>
            <div class="section-header-button">
                <a href="javascript:add()"
                   class="btn btn-primary btn-sm"><i
                        class="fa fa-plus mr-50"></i>
                    Tambah
                </a>
            </div>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{url('dashboard/jenis-penandatangan')}}">Daftar Jenis
                        Penandatangan</a></div>
                <div class="breadcrumb-item active">Visualisasi TTE</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-striped table-bordered" id="hideyori_datatable">
                                <thead>
                                <tr>
{{--                                    <th><input type="checkbox" id="check-all"></th>--}}
                                    <th>No.</th>
                                    <th>Judul Visualisasi</th>
                                    <th>Gambar TTE</th>
{{--                                    <th>Status</th>--}}
                                    <th>Actions</th>
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
                        <!--begin::Dropdown-->

                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- Modal -->
    <div
        class="modal fade"
        id="modal_form"
        {{--        tabindex="-1"--}}
        role="dialog"
        aria-labelledby="exampleModalScrollableTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
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

                            <div class="col-12">
                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label">
                                        <label>Judul Visualisasi</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="judul_visualisasi" id="judul_visualisasi" type="text">
                                        <input class="form-control" name="id_jenis_ttd_fk" id="id_jenis_ttd_fk" type="hidden" value="{{$row->id_jenis_ttd}}">
                                        <div class="invalid-feedback" id="error_judul_visualisasi">
                                        </div>
                                    </div>
                                </div>
                            </div>

{{--                            <div class="col-12">--}}
{{--                                <div class="form-group row">--}}
{{--                                    <div class="col-sm-3 col-form-label">--}}
{{--                                        <label>Status</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-sm-9">--}}
{{--                                        <select class="form-control" id="active"--}}
{{--                                                name="active">--}}
{{--                                            <option value=1>--}}
{{--                                                Aktif--}}
{{--                                            </option>--}}
{{--                                            <option value=0>--}}
{{--                                                Non Aktif--}}
{{--                                            </option>--}}
{{--                                        </select>--}}
{{--                                        <div class="invalid-feedback" id="error_active">--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

                            <div class="col-12">
                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label">
                                        <label>Upload Gambar TTE</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="file" id="img_visualisasi" class="form-control"
                                               name="img_visualisasi">
                                        <div class="invalid-feedback" id="error_img_visualisasi">
                                        </div>
                                        <p class="mt-2" id="img_visualisasi_text"></p>
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
@endsection

@push('scripts')
    <script src="{{assetku('assets/modules/datatables/datatables.min.js')}}"></script>
    <script
        src="{{assetku('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{assetku('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js')}}"></script>
    <script src="{{assetku('assets/modules/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{assetku('assets/modules/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{ assetku('magnific-popup/jquery.magnific-popup.min.js')}}"></script>
    <!--end::Page Vendors-->
    <!--begin::Page Scripts(used by this page)-->
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/mydatatable.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/deletertable.js')}}"></script>
    @include('components.buttonDatatables')
    <!--end::Page Vendors-->
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
                responsive: true,
                autoWidth: false,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari dan Tekan Enter..."
                },
                {{--ajax: "{{ route('jenis-penandatangan.data') }}",--}}
                ajax: {
                    url: "{{ url('dashboard/visualisasi-tte/data') }}",
                    type: "GET",
                    data: function (d) {
                        d.id_jenis_ttd_fk = '{{$row->id_jenis_ttd}}';
                    }
                },
                columns: [
                    // {
                    //     data: 'checkbox',
                    //     name: 'checkbox',
                    //     orderable: false,
                    //     searchable: false,
                    //     className: 'text-center'
                    // },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {data: 'judul_visualisasi', name: 'judul_visualisasi'},


                    {
                        data: 'img_visualisasi',
                        name: 'img_visualisasi',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    // {
                    //     data: 'active',
                    //     name: 'active',
                    //     className: 'text-center'
                    // },

                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
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


        function deleteData(paramId) {
            var url = '{{ url('dashboard/visualisasi-tte/delete/') }}';
            deleteDataTable(paramId, url);
        }


        function bulkDelete() {
            var url = '{{ url('dashboard/visualisasi-tte/bulkDelete/') }}';
            bulkDeleteTable(url)
        }

        $('#modal_form').on('shown.bs.modal', function () {
            $('#judul_visualisasi').focus()
        })

        function add() {
            $('#modal_form').modal();
            $('#modal_form').appendTo("body");
            $('#modal_form').modal('show'); // show bootstrap modal
            save_method = 'add';
            $('#form')[0].reset(); // reset form on modals
            $('.form-control').removeClass('is-invalid'); // clear error class
            $('.invalid-feedback').empty(); // clear error string
            $('#judul').text('FORM TAMBAH VISUALISASI TTE'); // Set Title to Bootstrap modal title
            $('#teksSimpan').text('Tambah');
            $('#btnsave').show();
            $('#img_visualisasi_text').text("");
            $('[name="judul_visualisasi"]').prop('disabled', false);
            $('[name="active"]').prop('disabled', false);
            $('[name="img_visualisasi"]').prop('disabled', false);
            $('#btnbatal').hide();
        }


        function save() {
            var url;
            var _method;
            var id;
            var formData = new FormData($('#form')[0]);

            id = '';
            url = "{{ url('dashboard/visualisasi-tte/create/') }}";
            _method = "POST";


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

                        reloadTable();
                        iziToast.success({
                            title: 'Sukses',
                            message: 'Berhasil Input Data',
                            position: 'topRight'
                        });
                        $('#modal_form').modal('hide');

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
