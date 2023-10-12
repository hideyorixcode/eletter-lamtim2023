<html lang="id">
<body>
<h2>Informasi Pemberitahuan Surat Disposisi</h2>
<span>Yth. Bapak/Ibu</span>,<br>
<span>Berikut ini kami sampaikan informasikan bahwa anda telah menerima sebuah surat disposisi dengan informasi sebagai berikut: </span><br>

<ul>
    <li>Nomor Surat : {{ $surat->no_surat }}</li>
    <li>Perihal : {{ $surat->perihal }}</li>
    <li>Tanggal Surat : {{ \Carbon\Carbon::parse($surat->tgl_surat)->isoFormat('dddd, D MMMM YYYY') }}</li>
    <li>Sifat Surat : <b>{{ \Illuminate\Support\Str::ucfirst($surat->sifat_surat)  }}</b></li>
</ul>
<br>
<span>Untuk melihat detail surat bisa anda lihat dengan mengklik tautan <a href="{{ url('surat-masuk/'. \Vinkla\Hashids\Facades\Hashids::encode($surat->id)) }}">berikut.</a></span>
<br>
<p>Salam Hormat,</p>

<b>Administrator Aplikasi {{ env('APP_NAME') }} {{ now()->format('Y') }}</b>

</body>

</html>
