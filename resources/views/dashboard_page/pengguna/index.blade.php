@extends('mylayouts.app')
@section('title', 'Pengguna')
@push('library-css')
    <!--begin::Page Vendors Styles(used by this page)-->
    <link rel="stylesheet" href="{{ assetku('magnific-popup/magnific-popup.min.css')}}">
    <style>
        .select_sm {
            height: 33.22222px!important;
            padding-bottom: 2px!important;
            padding-top: 2px!important;
            padding-right: 2px!important;
            padding-left: 2px!important;
        }
    </style>
    <!--end::Page Vendors Styles-->
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Daftar Pengguna</h1>
            <div class="section-header-button">
                <a href="{{ route('pengguna.form') }}"
                   class="btn btn-primary btn-sm"><i
                        class="fa fa-plus mr-50"></i>
                    Tambah
                </a>
            </div>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item">Daftar Pengguna</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-10">
                                    <form class="mb-2" action="javascript:getViewData()" id="formCari"
                                          name="formCari">
                                        <div class="row mb-2">
                                            <div class="col-lg-6 mb-lg-0 mb-6">
                                                <label>Pencarian :</label>
                                                <input type="search" class="form-control"
                                                       placeholder="cari nama pengguna / email"
                                                       id="textSearch" name="textSearch" autofocus>
                                            </div>

                                            @if(Auth::user()->level=='superadmin')
                                                <div class="col-lg-2 mb-lg-0 mb-6">
                                                    <label>Level :</label>
                                                    <select class="form-control" name="level"
                                                            id="level">
                                                        <option value="">Pilih</option>
                                                        <option value="superadmin">Super Admin</option>
                                                        <option value="admin">Admin</option>
                                                        <option value="umum">Umum</option>
                                                        <option value="adpim">Protokol Pimpinan</option>
                                                        <option value="sespri">Sespri</option>
                                                    </select>
                                                </div>
                                            @endif

                                            <div class="col-lg-2 mb-lg-0 mb-6">
                                                <label>&nbsp;</label>
                                                <button type="submit"
                                                        class="form-control btn btn-secondary"><i
                                                        class="fa fa-arrow-alt-circle-down mr-50"></i>
                                                    Filter
                                                </button>
                                            </div>


                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('components.loader')
                    <div id="renderviewData">

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/deleterview.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/myrenderview.js')}}"></script>
    <script src="{{ assetku('magnific-popup/jquery.magnific-popup.min.js')}}"></script>
    <!--begin::Page Scripts(used by this page)-->
    <script type="text/javascript">
        $(document).ready(function() {
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
            var urlData = "{{ url('dashboard/pengguna/data') }}";
            $.ajax({
                url: urlData,
                type: "GET",
                data:
                    {
                        page: pagenya,
                        level: $("#level").val(),
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
            var url = '{{ url('dashboard/pengguna/delete/') }}';
            deleteDataView(paramId, url);
        }

        function bulkDelete() {
            var url = '{{ url('dashboard/pengguna/bulkDelete/') }}';
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
