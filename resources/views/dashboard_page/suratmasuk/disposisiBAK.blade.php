@extends('mylayouts.app')
@section('title', 'Disposisi Surat Masuk')
@push('vendor-css')
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/datatables.min.css')}}">
    <link rel="stylesheet"
          href="{{assetku('assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/bootstrap-daterangepicker/daterangepicker.css')}}">
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{'Form Disposisi Surat Masuk'}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{route('surat-masuk')}}">Disposisi</a></div>
                <div class="breadcrumb-item active">Surat Masuk</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-sm-12 order-sm-0 order-lg-1 order-xl-1">
                    <div id="data_disposisi">

                    </div>

                </div>
                <div class="col-sm-12 order-sm-0 order-lg-0 order-xl-0">
                    <div class="card">
                        <div class="card-header">
                            <h4>Data Surat Masuk</h4>
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
            </div>
        </div>
    </section>
    <!-- Modal -->
@endsection
@push('scripts')
    <script src="{{assetku('assets/modules/datatables/datatables.min.js')}}"></script>
    <script
        src="{{assetku('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{assetku('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js')}}"></script>
    <script src="{{assetku('assets/modules/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{assetku('assets/modules/jquery-ui/jquery-ui.min.js')}}"></script>
    <!--end::Page Vendors-->
    <!--begin::Page Scripts(used by this page)-->
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/mydatatable.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/deletertable.js')}}"></script>
    <script src="{{assetku('assets/modules/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <script>
        function datepicker() {
            if (jQuery().daterangepicker) {
                if ($(".datetimepickerindo").length) {
                    $('.datetimepickerindo').daterangepicker({
                            locale: {format: 'DD/MM/YYYY HH:mm'},
                            singleDatePicker: true,
                            timePicker: true,
                            timePicker24Hour: true,
                            setDate: '',
                            //autoUpdateInput: false,

                        },
                        function (start, end, label) {
                            //$(".datetimepickerindo").val(start.format('DD/MM/YYYY HH:mm'))
                        });

                }

                if ($(".datetimepickerindokosong").length) {
                    $('.datetimepickerindokosong').daterangepicker({
                            locale: {format: 'DD/MM/YYYY HH:mm'},
                            singleDatePicker: true,
                            timePicker: true,
                            timePicker24Hour: true,
                            setDate: '',
                            clearBtn: true,
                            //autoUpdateInput: false,

                        },
                        function (start, end, label) {
                            //$(".datetimepickerindo").val(start.format('DD/MM/YYYY HH:mm'))
                        });
                    $('.datetimepickerindokosong').val("");

                }
            }
        }

        function deleteData(paramId) {
            var url = '{{ url('dashboard/disposisi/delete/') }}';
            deleteDataTable(paramId, url);
        }


        function getpenerima() {
            var urlData = "{{ url('dashboard/disposisi/get-penerima/'.$id) }}";
            $.ajax({
                url: urlData,
                type: "GET",
                success: function (data) {
                    $('[name="penerima"]').val(data).trigger('change');
                }
            });
        }

        $(document).ready(function() {
            getViewData();
            @if(session('pesan_status'))
            tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
            @endif
        });


        function getViewData() {
            $(".loaderData").show();
            var urlData = "{{ url('dashboard/disposisi/data-get/'.$id) }}";
            $.ajax({
                url: urlData,
                type: "GET",
                data:
                    {},
                success: function (data) {
                    $('#data_disposisi').html(data);
                    datepicker();
                    $(".select_cari").select2();
                    $(".loaderData").hide();

                }
            });
        }

        function batalkan_disposisi(paramId) {
            var id = paramId;
            var urlnya = '{{ url('dashboard/disposisi/delete/') }}';
            var token = $('meta[name="csrf-token"]').attr('content');
            Swal.fire({
                title: 'Apakah anda ingin membatalkan disposisi ini',
                text: "Data yang anda batalkan, tidak akan kembali",
                icon: 'warning',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
            }).then((result) => {
                if (result.value) {
                    $.ajax(
                        {
                            headers: {
                                'X-CSRF-TOKEN': token
                            },
                            url: urlnya + '/' + id,
                            type: 'DELETE',
                            dataType: "JSON",
                            data: {
                                "id": id,
                            },
                            success: function (response) {
                                if (response.status) {
                                    getViewData();
                                    iziToast.success({
                                        title: 'Sukses',
                                        message: response.pesan,
                                        position: 'topRight'
                                    });
                                } else {
                                    iziToast.error({
                                        title: 'Error',
                                        message: response.pesan,
                                        position: 'topRight'
                                    });
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

                } else if (result.dismiss === "cancel") {
                    iziToast.info('tidak jadi dibatalkan', 'Info');
                }
            });
        }

        function save() {
            var url;
            var formData = new FormData($('#form')[0]);
            url = "{{ url('dashboard/disposisi/create/') }}";
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
                        getViewData();
                        iziToast.success({
                            title: 'Sukses',
                            message: 'Berhasil Simpan Perubahan Disposisi',
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


    </script>

@endpush
