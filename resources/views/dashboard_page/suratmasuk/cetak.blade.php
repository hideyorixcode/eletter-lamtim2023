@extends('mylayouts.print')
@section('title', 'Cetak Data Surat Masuk')
@push('library-css')
@endpush
@section('content')
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center"><strong>CETAK QR-CODE
                    Surat {{$sifat_surat == 'biasa' ? 'Masuk' : ucwords($sifat_surat)}}</strong><br/>

            </td>
        </tr>
    </table>
    <br/>
    <table class="table table-bordered table-striped">
        <tbody>
        <tr>
            <td class="font-weight-bold">Kode</td>
            <td>{{$kode}}</td>
        </tr>
        <tr>
            <td class="font-weight-bold">Nomor Agenda</td>
            <td>{{$indek}}</td>
        </tr>
        <tr>
            <td class="font-weight-bold">No Surat</td>
            <td>{{$no_surat}}</td>
        </tr>
        <tr>
            <td class="font-weight-bold">Tanggal Surat</td>
            <td>{{$tgl_surat}}</td>
        </tr>
        <tr>
            <td class="font-weight-bold">Lampiran</td>
            <td>{{$lampiran}}</td>
        </tr>
        <tr>
            <td class="font-weight-bold">Dari</td>
            <td>{{$dari}}</td>
        </tr>
        <tr>
            <td class="font-weight-bold">Kepada</td>
            <td>{{cek_opd($kepada)->nama_opd}}</td>
        </tr>
        <tr>
            <td class="font-weight-bold">Hal</td>
            <td>{{$perihal}}</td>
        </tr>
        @if($catatan)
        <tr>
            <td class="font-weight-bold">Catatan Perjalanan Surat</td>
            <td>{{$catatan}}</td>
        </tr>
        @endif
        <tr>
            <td class="font-weight-bold">QR</td>
            <td>><img src="{{url('kodeqr/'.$qrcode)}}" alt=""/></td>
        </tr>
        </tbody>
    </table>

@endsection
@push('scripts')
@endpush
