@extends('mylayouts.app')
@section('title', 'Dashboard')
@push('library-css')
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Statistik / Penandatangan</h1>
        </div>

        <div class="section-body">


            <div class="row">

                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <label class="col-sm-3 col-lg-3 col-form-label">Tahun</label>
                                <div class="col-sm-5 col-lg-5">
                                    <select class="form-control"
                                            style="width: 100%;" name="tahun" id="tahun">
                                        <option disabled>Pilih</option>
                                        <option value="">Seluruh Tahun</option>
                                        <?php foreach ($data_tahun as $j) { ?>
                                        <option value="<?php echo $j->tahun ?>">
                                            <?php echo $j->tahun ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row" id="div_bulan">
                                <label class="col-sm-3 col-lg-3 col-form-label">Bulan</label>
                                <div class="col-sm-5 col-lg-5">
                                    <select class="form-control"
                                            style="width: 100%;" name="bulan" id="bulan">
                                        <option value="">Seluruh Bulan</option>
                                        <option value="01">Januari</option>
                                        <option value="02">Februari</option>
                                        <option value="03">Maret</option>
                                        <option value="04">April</option>
                                        <option value="05">Mei</option>
                                        <option value="06">Juni</option>
                                        <option value="07">Juli</option>
                                        <option value="08">Agustus</option>
                                        <option value="09">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                            </div>


                            <div class="row" style="padding-top: 20px;">
                                <label class="col-sm-3 col-lg-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-5 col-lg-5">
                                    <button class="btn btn-outline-info btn-sm btn-block"
                                            onclick="reload_table()" type="button"
                                            id="btnubah">
                                        <i
                                            class="fa fa-undo"></i> Saring Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div id="container">

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
        nampilin_grafik();
        $('#div_bulan').hide();

        function list_bulan() {
            var tahun = $('[name="tahun"]').val();
            if (tahun == "") {
                $('#div_bulan').hide();
            } else {
                $('#div_bulan').show();
            }
        }

        $('select[name="tahun"]').on('change', function () {
            list_bulan();

        });

        function reload_table() {
            nampilin_grafik();
        }

        function nampilin_grafik() {

            tahun = $('[name="tahun"]').val();
            bulan = $('[name="bulan"]').val();
            var judul = $("#bulan option:selected").text() + ' - ' + $("#tahun option:selected").text();
            $.ajax({
                type: 'GET',
                url: '{{url('dashboard/statistik/grafik-tanda-tangan')}}',
                data: {
                    tahun: tahun,
                    bulan: bulan,
                },
                success: function (respon) {
                    console.log(respon);
                    var actualData = JSON.parse(respon);
                    if (Array.isArray(actualData) && actualData.length) {
                        iziToast.success({
                            title: 'Sukses',
                            message: 'Data Ditemukan',
                            position: 'topRight'
                        });
                        // Swal.fire(
                        //     {
                        //         title: "Memuat Data",
                        //         type: "success",
                        //         showConfirmButton: false,
                        //         timer: 1000,
                        //     }
                        // );

                        const categories = actualData.map((o) => (o.jenis_ttd));
                        const ttd = actualData.map((o) => (o.kategori_ttd));
                        const datanya = actualData.map((o) => Number(o.jumlah));
                        console.log(categories);
                        console.log(ttd);
                        console.log(datanya);

                        $("#container").show();
                        var chart = Highcharts.chart('container', {
                            title: {
                                text: 'Jumlah Penandatangan'
                            },
                            chart: {
                                type: 'column'
                            },

                            subtitle: {
                                text: judul
                            },

                            credits: {
                                enabled: false
                            },

                            xAxis: {
                                labels: {
                                    step: 1
                                },
                                categories: categories,


                            },

                            yAxis: {
                                // allowDecimals: false,
                                labels: {
                                    style: {
                                        fontSize: '9px',
                                        width: '175px'
                                    }
                                },
                                title: {
                                    text: 'Jumlah'
                                }
                            },

                            series: [

                                {
                                    name: 'Tanda Tangan Basah',
                                    data: [3, 4, 5, 6]

                                }, {
                                    name: 'Tanda Tangan Elektronik',
                                    data: [5, 6, 8, 2]

                                },
                            ],

                            // series: [{
                            //     name: 'Jumlah',
                            //     type: 'column',
                            //     stacking: 'normal',
                            //     // pointWidth: 50,
                            //     // pointPadding: 0,
                            //     colorByPoint: true,
                            //     data: datanya,
                            //     showInLegend: false
                            // }]

                        });
                    } else {
                        $("#container").hide();
                        // Swal.fire({
                        //     title: "Tidak Ditemukan Data",
                        //     type: "error",
                        //     showConfirmButton: false,
                        //     timer: 1000,
                        // });
                        iziToast.info({
                            title: 'Info',
                            message: 'Tidak Ditemukan Data',
                            position: 'topRight'
                        });
                    }


                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(jqXHR.status);

                }
            });


        }
    </script>
@endpush
