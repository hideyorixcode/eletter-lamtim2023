@extends('mylayouts.front')
@section('title', 'Detail Surat Keluar ')
@push('library-css')
@endpush
@section('content')
    <section class="section mt-3">
        <div class="section-body">
            <div class="row">
                <div class="col-sm-4 order-sm-0 order-lg-1 order-xl-1">
                    <div class="card">
                        <div class="card-body">
                            <div class="empty-state">
                                @if($berkas)
                                    @if($is_download==1)
                                            <?php
                                            if ($status_sk == 'draft') {
                                                if ($kategori_ttd == 'elektronik') {
                                                    $link = url('berkas/temp/' . $berkas);
                                                } else {
                                                    $link = url('berkas/' . $berkas);
                                                }
                                            } else {
                                                $link = url('berkas/' . $berkas);
                                            }
                                            ?>
                                        <a href="{{$link}}" target="_blank">
                                            <img class="img-fluid" style="height: 75px"
                                                 src="{{url('uploads/pdf_icon.png')}}"
                                                 alt="image">
                                            <h2 class="mt-2 mb-2">Download Berkas</h2>
                                        </a>
                                    @else
                                        <img class="img-fluid"
                                             src="{{url('kodeqr/'.$qrcode)}}"
                                             alt="{{$no_surat}}">
                                        <h2 class="mt-2 mb-2">Surat Keluar </h2>
                                    @endif
                                @else
                                    <img class="img-fluid"
                                         src="{{url('kodeqr/'.$qrcode)}}"
                                         alt="{{$no_surat}}">
                                    <h2 class="mt-2 mb-2">Surat Keluar </h2>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8 order-sm-0 order-lg-0 order-xl-0">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-sm-12">

                                @php
                                    if($status_sk=='final')
                                    {
                                    $alert = 'primary';
                                    $teksalert = 'Status Dokumen : Final';
                                    }
                                    else if($status_sk=='draft')
                                    {
                                    $alert = 'danger';
                                    $teksalert = 'Status Dokumen : Draft';
                                    }
                                    else
                                    {
                                    $alert ='warning';
                                    $teksalert = 'Status Dokumen : Revisi';
                                    }
                                @endphp
                                <div class="alert alert-{{$alert}}">
                                    {{$teksalert}}
                                </div>

                            </div>
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">1. No Surat</label>
                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {!!  $no_surat!='' ? $no_surat : '
                                        <div class="p-1 color bg-danger text-white"> Belum Diberikan Nomor </div>
                                        ' !!}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">2. Tgl Surat</label>
                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$tgl_surat}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">3. Dari Perangkat Daerah /
                                        Pimpinan</label>
                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$nama_opd}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">4. Kepada</label>
                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        @php
                                            $kepadaOke = $kepada;
                                            if ($tujuan == 'dalam' || $tujuan == 'keduanya') {
                                            $kepadaOke = $kepada_opd.';';
                                            $kepadaOke .= $kepada;
                                            $explode_kepada = explode(';', rtrim($kepadaOke, ';'));
                                            $kepada = '';
                                            foreach ($explode_kepada as $value) {
                                            $kepada .= '- ' . $value . ' <br/>';
                                            }
                                            }
                                        @endphp
                                        {!! $kepada !!}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">5. Lampiran</label>
                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$lampiran}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">6. Hal</label>
                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$perihal}}
                                    </label>
                                </div>
                            </div>
                            @if($jenis_ttd)
                                <hr class="mb-2">
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-5 col-lg-5 col-form-label">7. Ditandangani Oleh</label>
                                        <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                            {{$jenis_ttd->jenis_ttd.' - '.cek_opd($jenis_ttd->id_opd_fk)->nama_opd}}
                                        </label>
                                    </div>
                                </div>
                            @endif
                            @if($kategori_ttd)
                                <hr class="mb-2">
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-5 col-lg-5 col-form-label">8. Metode Tanda Tangan</label>
                                        <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                            {{$kategori_ttd=='basah' ? 'TANDA TANGAN BASAH' : 'TANDA TANGAN ELEKTRONIK'}}
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
@endpush
