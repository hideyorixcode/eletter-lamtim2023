<html lang="id">
<body>
<h2>Informasi Pemberitahuan Dokumen</h2>
<span>Yth. Bapak/Ibu</span>,<br>
<span>Berikut ini kami sampaikan informasi dokumen yang perlu ditandatangani oleh anda secara elektronik dengan informasi sebagai berikut: </span><br>

<ul>
    <li>Nomor Dokumen : {{ $dokumen->no_surat }}</li>
    <li>Perihal : {{ $dokumen->perihal }}</li>
    <li>Tanggal Dokumen : {{ \Carbon\Carbon::parse($dokumen->tgl_surat)->isoFormat('dddd, D MMMM YYYY') }}</li>
</ul>
<br>
<span>Untuk melihat detail dokumen bisa anda lihat dengan mengklik tautan <a href="{{ url('tanda-tangan-dokumen/'. \Vinkla\Hashids\Facades\Hashids::encode($dokumen->id)) }}">berikut.</a></span>
<br>
<p>Salam Hormat,</p>

<b>Administrator Aplikasi {{ env('APP_NAME') }} {{ now()->format('Y') }}</b>

</body>

</html>
