@extends('mylayouts.app')
@section('title', 'PTHL')
@push('vendor-css')
    <!--begin::Page Vendors Styles(used by this page)-->
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/datatables.min.css')}}">
    <link rel="stylesheet"
          href="{{assetku('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Buttons/css/buttons.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets//modules/bootstrap-daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{ assetku('magnific-popup/magnific-popup.min.css')}}">
    <!--end::Page Vendors Styles-->
@endpush
@push('library-css')
@endpush
@section('content')
    <!--begin::Card-->

    <section class="section">
        <div class="section-header">
            <h1>SK PNS Jabatan Pelaksana</h1>
            @if(in_array(Auth::user()->level, ['admin', 'superadmin','sespri']))
                <div class="section-header-button">
                    <a href="javascript:add()"
                       class="btn btn-primary btn-sm"><i
                            class="fa fa-plus mr-50"></i>
                        Tambah Penomoran Dokumen
                    </a>
                </div>
            @endif
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item">SK PNS Jabatan Pelaksana</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">

                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="form-row mb-3">
                                        <div class="col-lg-12">
                                            <label>Dokumen :</label>
                                            <select class="select_cari form-control" id="select_document"
                                                    name="select_document">
                                                @foreach($listDokumen as $x)
                                                    <option
                                                        value="{{$x->dokumen_id}}">{{$x->dokumen_nomor.' ['.$x->dokumen_hal.']'}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-row mb-3">
                                        <div class="col-lg-12">
                                            <label>Instansi :</label>
                                            <select class="select_cari form-control" id="select_opd"
                                                    name="select_opd">
                                                <option value="">Seluruh Instansi</option>
                                                @foreach($listOpd as $z)
                                                    <option
                                                        value="{{$z->id_opd}}">{{$z->nama_opd}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-row mb-3">
                                        <div class="col-lg-12">
                                            <label>Jumlah Tampilan Data :</label>
                                            <select class="form-control" name="page_count"
                                                    id="page_count">
                                                <option disabled>Jumlah Tampil</option>
                                                @foreach($paginateList as $value)
                                                    <option
                                                        value={{$value}}>{{$value == -1 ? 'ALL' : $value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                {{--                                <div class="col-lg-12 text-right">--}}
                                {{--                                    <button class="btn btn-outline-info btn-sm" onclick="getViewData(1)"--}}
                                {{--                                            type="button"--}}
                                {{--                                            id="btnubah">--}}
                                {{--                                        <i class="fa fa-undo"></i> Saring Data--}}
                                {{--                                    </button>--}}
                                {{--                                </div>--}}
                            </div>
                        </div>

                    </div>
                </div>

                @include('components.loader')
                <div class="col-md-12">
                    <div id="renderviewData">

                    </div>
                </div>

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
                                            <label>Pilih Dokumen</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="select_cari form-control" id="dokumen_id"
                                                    name="dokumen_id">
                                                @foreach($listDokumen as $x)
                                                    <option
                                                        value={{$x->dokumen_id}}>{{$x->dokumen_nomor}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="error_dokumen_id">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-sm-3 col-form-label">
                                            <label>Instansi</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <select class="select_cari form-control" id="opd_id"
                                                    name="opd_id">
                                                <option value="">-PILIH-</option>
                                                @foreach($listOpd as $z)
                                                    <option
                                                        value="{{$z->id_opd}}">{{$z->nama_opd}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="error_opd_id">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-sm-3 col-form-label">
                                            <label>Nomor Dokumen</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="hidden" id="id" class="form-control"
                                                   name="id">
                                            <input type="text" id="nomor_dokumen" class="form-control"
                                                   name="nomor_dokumen">
                                            <div class="invalid-feedback" id="error_nomor_dokumen">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-sm-3 col-form-label">
                                            <label>Tanggal Dokumen</label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" id="tanggal_dokumen" class="form-control"
                                                   name="tanggal_dokumen">
                                            <div class="invalid-feedback" id="error_tanggal_dokumen">
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
    @endif
@endsection

@push('scripts')

    <script src="{{assetku('assets/modules/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{assetku('assets/modules/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{ assetku('assets/modules/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <!--end::Page Vendors-->
    <!--begin::Page Scripts(used by this page)-->
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/deleterview.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/myrenderview.js')}}"></script>
    <!--end::Page Vendors-->
    <!--begin::Page Scripts(used by this page)-->
    <script type="text/javascript">

        $(document).ready(function () {
            getViewData(1);
            @if(session('pesan_status'))
            tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
            @endif
        });

        $(document).on('click', '.pagination a', function (event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            getViewData(page);
        });

        function getViewData(page) {
            var pagenya = page ? page : 1;
            $(".loaderData").show();
            var urlData = "{{ url('dashboard/pns/data-penomoran-pelaksana') }}";
            $.ajax({
                url: urlData,
                type: "GET",
                data:
                    {
                        page: pagenya,
                        opd_id: $("#select_opd").val(),
                        arrayList: arrayList,
                        page_count: $("#page_count").val(),
                    },
                success: function (data) {
                    $('#renderviewData').html(data);
                    $(".loaderData").hide();
                    initCountpage();
                    initClick();
                }
            });
        }




        @if(in_array(Auth::user()->level, ['admin', 'superadmin','sespri']))
        function deleteData(paramId) {
            var url = '{{ url('dashboard/pns/delete-penomoran-pelaksana/') }}';
            deleteDataView(paramId, url);
        }

        $('#modal_form').on('shown.bs.modal', function () {
            $('#nomor_dokumen').focus()
        })

        $('#select_opd').on('change', function () {
            getViewData(1);
        });

        $('#select_document').on('change', function () {
            getViewData(1);
        });

        if (jQuery().daterangepicker) {
            if ($("#tanggal_dokumen").length) {
                $('#tanggal_dokumen').daterangepicker({
                    locale: {format: 'DD/MM/YYYY'},
                    singleDatePicker: true,
                });
            }
        }

        function add() {
            select_doc = $('#select_document').val();
            select_opd = $('#select_opd').val();
            $('#modal_form').modal();
            $('#modal_form').appendTo("body");
            $('#modal_form').modal('show'); // show bootstrap modal
            save_method = 'add';
            $('#form')[0].reset(); // reset form on modals
            $('.form-control').removeClass('is-invalid'); // clear error class
            $('.invalid-feedback').empty(); // clear error string
            $('#judul').text('FORM TAMBAH PENOMORAN DOKUMEN'); // Set Title to Bootstrap modal title
            $('#teksSimpan').text('Tambah');
            $('#btnsave').show();
            $('[name="tanggal_dokumen"]').prop('disabled', false);
            $('[name="nomor_dokumen"]').prop('disabled', false);
            $('[name="opd_id"]').prop('disabled', false);
            $('[name="dokumen_id"]').prop('disabled', false);
            $('[name="dokumen_id"]').val(select_doc).trigger('change');
            $('[name="opd_id"]').val(select_opd).trigger('change');
            $('#btnbatal').hide();
        }

        function initClick() {
            $(".clickable-edit").click(function () {
                save_method = 'update';
                id = $(this).attr('data-id');
                //alert(id);
                dokumen_id = $(this).attr('data-dokumen_id');
                tanggal_dokumen = $(this).attr('data-tanggal_dokumen');
                nomor_dokumen = $(this).attr('data-nomor_dokumen');
                tentang_dokumen = $(this).attr('data-tentang_dokumen');
                opd_id = $(this).attr('data-opd_id');
                $('#form')[0].reset(); // reset form on modals
                $('.form-control').removeClass('is-invalid'); // clear error class
                $('.invalid-feedback').empty(); // clear error string
                $('#modal_form').modal();
                $('#modal_form').appendTo("body");
                $('#modal_form').modal('show'); // sh
                //alert(pthl_id);
                $('[name="id"]').val(id);
                $('[name="dokumen_id"]').val(dokumen_id).trigger('change');
                $('[name="opd_id"]').val(opd_id).trigger('change');
                $('[name="tanggal_dokumen"]').val(tanggal_dokumen);
                $('[name="nomor_dokumen"]').val(nomor_dokumen);
                $('#judul').text('FORM UBAH PENOMORAN'); // Set Title to Bootstrap modal titlep modal title
                $('#teksSimpan').text('Simpan Perubahan');
                //$('#dokumen_file_text').text(dokumen_file);
                $('#btnsave').show();
                $('[name="tanggal_dokumen"]').prop('disabled', false);
                $('[name="nomor_dokumen"]').prop('disabled', false);
                $('[name="opd_id"]').prop('disabled', false);
                $('[name="dokumen_id"]').prop('disabled', false);
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
                url = "{{ url('dashboard/pns/create-penomoran-pelaksana/') }}";
                _method = "POST";
            } else {
                id = $('[name="id"]').val();
                url = '{{ url('dashboard/pns/update-penomoran-pelaksana/') }}' + '/' + id;
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
                            getViewData(1);
                            iziToast.success({
                                title: 'Sukses',
                                message: 'Berhasil Input Data',
                                position: 'topRight'
                            });
                            $('#modal_form').modal('hide');
                        } else {
                            getViewData(1);
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

        @endif

    </script>
@endpush
