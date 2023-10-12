@extends('mylayouts.app')
@section('title', 'Dashboard')
@push('library-css')
    <style>
        .highcharts-figure,
        .highcharts-data-table table {
            min-width: 310px;
            /*max-width: 800px;*/
            margin: 1em auto;
        }

        #container {
            height: 400px;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #ebebeb;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }

        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }

        .highcharts-data-table td,
        .highcharts-data-table th,
        .highcharts-data-table caption {
            padding: 0.5em;
        }

        .highcharts-data-table thead tr,
        .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }

        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }

    </style>
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Dashboard Administrator</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="javascript:void(0)">Dashboard Administrator</a></div>
            </div>
        </div>

        <div class="section-body">


            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-4 col-12 mb-3">
                            <div class="card card-statistic-1 h-75">
                                <div class="card-icon bg-primary">
                                    <i class="far fa-user"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Jenis Penandatangan</h4>
                                    </div>
                                    <div class="card-body">
                                        {{format_angka_indo($jenisttd)}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12 mb-3">
                            <div class="card card-statistic-1 h-75">
                                <div class="card-icon bg-danger">
                                    <i class="far fa-building"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Pimpinan & Instansi</h4>
                                    </div>
                                    <div class="card-body">
                                        {{format_angka_indo($perangkatdaerah)}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12 mb-3">
                            <div class="card card-statistic-1 h-75">
                                <div class="card-icon bg-warning">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Total Surat Masuk</h4>
                                    </div>
                                    <div class="card-body">
                                        {{format_angka_indo($suratmasuk)}}
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-4 col-12 mb-3">
                            <div class="card card-statistic-1 h-75">
                                <div class="card-icon bg-info">
                                    <i class="far fa-paper-plane"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Total Surat Keluar Metode Tanda Tangan Basah</h4>
                                    </div>
                                    <div class="card-body">
                                        {{format_angka_indo($suratkeluarbasah)}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12 mb-3">
                            <div class="card card-statistic-1 h-75">
                                <div class="card-icon bg-success">
                                    <i class="fas fa-signature"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Total Surat Keluar Metode Tanda Tangan Elektronik</h4>
                                    </div>
                                    <div class="card-body">
                                        {{format_angka_indo($suratkeluartte)}}
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card card-hero">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class="far fa-question-circle"></i>
                            </div>
                            <h4>Pemberitahuan</h4>
                            <div class="card-description">Perlu Tindak Lanjut</div>
                        </div>
                        <div class="card-body p-4">

                            <nav>
                                <div class="nav nav-tabs" id="nav-tab">
                                    <a class="nav-item nav-link active" id="nav-masuk-tab" data-toggle="tab"
                                       href="#nav-masuk" role="tab">Surat Masuk</a>
                                    <a class="nav-item nav-link" id="nav-keluar-tab" data-toggle="tab"
                                       href="#nav-keluar" role="tab">Surat Keluar</a>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-masuk" role="tabpanel">
                                    <div class="tickets-list overflow-auto" style="height:300px">
                                        @if(count($listSMbiasa)>0)
                                            @foreach($listSMbiasa as $biasa)
                                                <a href="{{url('dashboard/disposisi/'.Vinkla\Hashids\Facades\Hashids::encode($biasa->id))}}" class="ticket-item">
                                                    <div class="ticket-title">
                                                        <h4>{{$biasa->no_surat}}</h4>
                                                    </div>
                                                    <div class="ticket-info">
                                                        <div>Tanggal Surat</div>
                                                        <div class="bullet"></div>
                                                        <div class="text-primary">{{TanggalIndo2($biasa->tgl_surat)}}</div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-keluar" role="tabpanel">
                                    <div class="tickets-list overflow-auto" style="height:300px">
                                        @if(count($listSK)>0)
                                            @foreach($listSK as $keluar)
                                                <a href="{{url('surat-keluar/'.Vinkla\Hashids\Facades\Hashids::encode($keluar->id))}}" target="_blank" class="ticket-item">
                                                    <div class="ticket-title">
                                                        <h4>{{$keluar->no_surat}}</h4>
                                                    </div>
                                                    <div class="ticket-info">
                                                        <div>Tanggal Surat</div>
                                                        <div class="bullet"></div>
                                                        <div class="text-primary">{{TanggalIndo2($keluar->tgl_surat)}}</div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                            </div>


                        </div>
                    </div>
                </div>


                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card card-primary">

                        <div class="card-header">
                            <h4>Filter Statistik</h4>
                        </div>

                        <div class="card-body">

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Tahun</label>
                                        <div class="col-sm-6 col-lg-6">
                                            <select class="form-control" id="select_tahun" name="select_tahun">
                                                @for($i=date('Y');$i>=2010;$i--)
                                                    <option value={{$i}}>{{$i}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Pimpinan / Instansi</label>
                                        <div class="col-sm-6 col-lg-6">
                                            <select class="form-control" id="select_perangkat" name="select_perangkat">
                                                <option value="">Seluruh Instansi</option>
                                                @foreach($listPerangkat as $perangkat)
                                                    <option
                                                        value="{{ $perangkat->id_opd }}">{{ $perangkat->nama_opd }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-3 col-lg-3">
                                            <button id="btnfilter" class="btn btn-sm btn-success" type="button"
                                                    onclick="getViewData()">FILTER
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    @include('components.loader')
                                    <figure class="highcharts-figure" id="renderview">

                                    </figure>
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
    <script src="{{assetku('highchart/highcharts.js')}}"></script>
    <script src="{{assetku('highchart/highcharts-more.js')}}"></script>
    <script src="{{assetku('highchart/modules/exporting.js')}}"></script>

    <script type="text/javascript">

        $(document).ready(function () {
            getViewData();
        });


        function getViewData() {
            $(".loaderData").show();
            var urlData = "{{ url('dashboard/statistik/') }}";
            $.ajax({
                url: urlData,
                type: "GET",
                data:
                    {
                        tahun: $("#select_tahun").val(),
                        perangkat: $("#select_perangkat").val(),
                        namaperangkat: $("#select_perangkat option:selected").text(),
                    },
                success: function (hasil) {
                    console.log(hasil);
                    $('#renderview').html(hasil);
                    $(".loaderData").hide();

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.statusText);
                    console.log(thrownError);
                }
            });
        }


    </script>
@endpush
