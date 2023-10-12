<div id="container_grafik"></div>
<script>
    $(document).ready(function () {
        $('#container_grafik').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Statistik Penandatangan'
            },
            subtitle: {
                text: 'Total Seluruh {{$tahun}}'
            },
            xAxis: {
                categories: [
                    //'Jan',
                    @foreach($jenisttd as $ttd)
                        '{{$ttd->jenis_ttd}}',
                    @endforeach
                ],
                crosshair: true,
                allowDecimals: false
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Jumlah'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y} </b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [
                {

                    name: 'Tanda Tangan Basah',
                    data: [
                        @foreach($jenisttd as $ttd)
                            {{getJumlahJenisTTD($ttd->id_jenis_ttd, 'basah', $tahun, $bulan).','}}
                            @endforeach
                    ]

                }, {
                    name: 'Tanda Tangan Elektronik',
                    data: [
                        @foreach($jenisttd as $ttd)
                            {{getJumlahJenisTTD($ttd->id_jenis_ttd, 'elektronik', $tahun, $bulan).','}}
                            @endforeach
                    ]


                },
            ],
        });
    });

</script>
