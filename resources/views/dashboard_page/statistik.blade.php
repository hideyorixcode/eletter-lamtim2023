<div id="container_grafik"></div>
<script>
    $(document).ready(function () {


        $('#container_grafik').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Statistik Bulanan Dokumen {{$namaperangkat}}'
            },
            subtitle: {
                text: 'Total Seluruh {{$tahun}}'
            },
            xAxis: {
                categories: [
                    'Jan',
                    'Feb',
                    'Mar',
                    'Apr',
                    'Mei',
                    'Jun',
                    'Jul',
                    'Aug',
                    'Sep',
                    'Okt',
                    'Nov',
                    'Desember'
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
                    name: 'Surat Masuk',
                    data: [{{$sm_01.','.$sm_02.','.$sm_03.','.$sm_04.','.$sm_05.','.$sm_06.','.$sm_07.','.$sm_08.','.$sm_09.','.$sm_10.','.$sm_11.','.$sm_12}}]

                }, {
                    name: 'Surat Keluar',
                    data: [{{$sk_01.','.$sk_02.','.$sk_03.','.$sk_04.','.$sk_05.','.$sk_06.','.$sk_07.','.$sk_08.','.$sk_09.','.$sk_10.','.$sk_11.','.$sk_12}}]

                },
            ],
        });
    });

</script>
