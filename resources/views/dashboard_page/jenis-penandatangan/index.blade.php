@extends('mylayouts.app')
@section('title', 'Penandatangan')
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
            <h1>Daftar Penandatangan</h1>
            <div class="section-header-button">
                <a href="javascript:add()"
                   class="btn btn-primary btn-sm"><i
                        class="fa fa-plus mr-50"></i>
                    Tambah
                </a>
            </div>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item">Daftar Penandatangan</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="d-block">Tampilkan</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="inlineradio1" name="tampilkan" value=""
                                   checked onchange="reloadTable();">
                            <label class="form-check-label" for="inlineradio1">Seluruh Data</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="inlineradio2" name="tampilkan" value="tte"
                                   onchange="reloadTable();">
                            <label class="form-check-label" for="inlineradio2">Memiliki TTE</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="inlineradio3" name="tampilkan"
                                   value="tidak" onchange="reloadTable();">
                            <label class="form-check-label" for="inlineCheckbox3">Tidak Memiliki TTE</label>
                        </div>
                    </div>

                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-striped table-bordered" id="hideyori_datatable">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" id="check-all"></th>
                                    <th>No.</th>
                                    <th>NIK</th>


                                    <th>Penandatangan</th>
                                    <th>Pimpinan/Perangkat Daerah</th>
                                    <th>Gambar TTE</th>
                                    <th>Status</th>
                                    <th>Username E-Letter</th>
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
                                        <label>Penandatangan</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="hidden" id="id_jenis_ttd" class="form-control"
                                               name="id_jenis_ttd">
                                        <input type="hidden" id="id" class="form-control"
                                               name="id">
                                        <input type="text" id="jenis_ttd" class="form-control"
                                               name="jenis_ttd">
                                        <div class="invalid-feedback" id="error_jenis_ttd">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label">
                                        <label>NIK</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="number" id="nik" class="form-control"
                                               name="nik"
                                               placeholder="NIK sebagai verifikasi TTE">
                                        <div class="invalid-feedback" id="error_nik">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label">
                                        <label>E-Mail</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="email" id="email" class="form-control"
                                               name="email"
                                               placeholder="Email Penandatangan">
                                        <div class="invalid-feedback" id="error_email">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label">
                                        <label>Pimpinan / Perangkat Daerah</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="select_cari form-control" id="id_opd_fk"
                                                name="id_opd_fk">
                                            @foreach($listPerangkat as $nama => $value)
                                                <option
                                                    value={{$value}}>{{$nama}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="error_id_opd_fk">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-12">
                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label">
                                        <label>Status</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="active"
                                                name="active">
                                            <option value=1>
                                                Aktif
                                            </option>
                                            <option value=0>
                                                Non Aktif
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" id="error_active">
                                        </div>
                                    </div>
                                </div>
                            </div>

<!--
                            <div class="col-12">
                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label">
                                        <label>Upload Gambar TTE</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="file" id="img_ttd" class="form-control"
                                               name="img_ttd">
                                        <div class="invalid-feedback" id="error_img_ttd">
                                        </div>
                                        <p class="mt-2" id="img_ttd_text"></p>
                                    </div>
                                </div>
                            </div>
-->


                            <label class="font-weight-bold mt-4">Akun login aplikasi E-Letter</label>

                            <div class="col-12">
                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label">
                                        <label>Username</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" id="username" class="form-control"
                                               name="username">
                                        <div class="invalid-feedback" id="error_username">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label">
                                        <label>Password</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="password" id="password" class="form-control"
                                               name="password">
                                        <div class="invalid-feedback" id="error_password">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group row">
                                    <div class="col-sm-3 col-form-label">
                                        <label>Konfirmasi Password</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="password" id="password_confirmation" class="form-control"
                                               name="password_confirmation">
                                        <div class="invalid-feedback" id="error_password_confirmation">
                                        </div>
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
                    url: "{{ route('jenis-penandatangan.data') }}",
                    type: "GET",
                    data: function (d) {
                        d.tampilkan = $('input[name="tampilkan"]:checked').val();
                    }
                },
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
                    {data: 'nik', name: 'nik'},

                    {data: 'jenis_ttd', name: 'jenis_ttd', responsivePriority: -1},
                    {data: 'id_opd_fk', name: 'id_opd_fk'},
                    {data: 'img_ttd', name: 'img_ttd', orderable: false, searchable: false, className: 'text-center'},
                    {
                        data: 'active',
                        name: 'active',
                        className: 'text-center'
                    },
                    {data: 'username', name: 'users.username'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
                ],

                rowCallback: function (row, data, index) {
                    cellValue = data['id_jenis_ttd'];
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
                            exporni = '1,2,3,4,6,7';
                        @else
                            exporni = '0,1,2,3,5,6';
                        @endif

                        var buttons_dom = new $.fn.dataTable.Buttons(table, {
                            buttons: [
                                {
                                    extend: 'print',
                                    text: 'Print',
                                    title: 'Penandatangan',
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
                                    title: 'Penandatangan',
                                    exportOptions: {
                                        //columns: ':visible'
                                        columns: exporni
                                    }
                                },
                                {
                                    extend: 'excelHtml5',
                                    text: 'Excel',
                                    title: 'Penandatangan',
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
                    initClick();
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
                initClick();
                initMagnific();
            });


        });


        function deleteData(paramId) {
            var url = '{{ url('dashboard/jenis-penandatangan/delete/') }}';
            deleteDataTable(paramId, url);
        }


        function bulkDelete() {
            var url = '{{ url('dashboard/jenis-penandatangan/bulkDelete/') }}';
            bulkDeleteTable(url)
        }

        $('#modal_form').on('shown.bs.modal', function () {
            $('#jenis_ttd').focus()
        })

        function add() {
            $('#modal_form').modal();
            $('#modal_form').appendTo("body");
            $('#modal_form').modal('show'); // show bootstrap modal
            save_method = 'add';
            $('#form')[0].reset(); // reset form on modals
            $('.form-control').removeClass('is-invalid'); // clear error class
            $('.invalid-feedback').empty(); // clear error string
            $('#judul').text('FORM TAMBAH PENANDATANGAN'); // Set Title to Bootstrap modal title
            $('#teksSimpan').text('Tambah');
            $('#btnsave').show();
            $('#img_ttd_text').text("");
            $('[name="jenis_ttd"]').prop('disabled', false);
            $('[name="active"]').prop('disabled', false);
            $('[name="img_ttd"]').prop('disabled', false);
            $('[name="nik"]').prop('disabled', false);
            $('[name="username"]').prop('disabled', false);
            $('[name="id_opd_fk"]').prop('disabled', false);
            $('[name="id_jenis_ttd_fk"]').val(1).trigger('change');
            $('#btnbatal').hide();
        }

        function initClick() {
            $(".clickable-edit").click(function () {
                save_method = 'update';
                id_jenis_ttd = $(this).attr('data-id_jenis_ttd');
                jenis_ttd = $(this).attr('data-jenis_ttd');
                img_ttd = $(this).attr('data-img_ttd');
                id_opd_fk = $(this).attr('data-id_opd_fk');
                nik = $(this).attr('data-nik');
                username = $(this).attr('data-username');
                email = $(this).attr('data-email');
                id = $(this).attr('data-id');
                active = $(this).attr('data-active');
                $('#form')[0].reset(); // reset form on modals
                $('.form-control').removeClass('is-invalid'); // clear error class
                $('.invalid-feedback').empty(); // clear error string
                $('#modal_form').modal();
                $('#modal_form').appendTo("body");
                $('#modal_form').modal('show'); // sh
                $('[name="id_jenis_ttd"]').val(id_jenis_ttd);
                $('[name="id"]').val(id);
                $('[name="email"]').val(email);
                $('[name="jenis_ttd"]').val(jenis_ttd);
                $('[name="nik"]').val(nik);
                $('[name="username"]').val(username);
                $('[name="id_opd_fk"]').val(id_opd_fk).trigger('change');
                $('#judul').text('FORM UBAH PENANDATANGAN'); // Set Title to Bootstrap modal titlep modal title
                $('#teksSimpan').text('Simpan Perubahan');
                $('#img_ttd_text').text(img_ttd);
                $('#btnsave').show();
                $('[name="jenis_ttd"]').prop('disabled', false);
                $('[name="active"]').prop('disabled', false);
                $('[name="img_ttd"]').prop('disabled', false);
                $('[name="nik"]').prop('disabled', false);
                $('[name="id_opd_fk"]').prop('disabled', false);
                $('[name="id"]').prop('disabled', false);
                $('[name="email"]').prop('disabled', false);
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
                url = "{{ url('dashboard/jenis-penandatangan/create/') }}";
                _method = "POST";
            } else {
                id = $('[name="id_jenis_ttd"]').val();
                url = '{{ url('dashboard/jenis-penandatangan/update/') }}' + '/' + id;
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
