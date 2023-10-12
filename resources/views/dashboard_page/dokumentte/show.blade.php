@extends('mylayouts.front')
@section('title', 'Detail Dokumen TTE ')
@push('library-css')

@endpush
@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-sm-4 order-sm-0 order-lg-1 order-xl-1">
                    <div class="card">
                        <div class="card-body">
                            <div class="empty-state">
                                @if($berkas)
                                    <?php
                                    if ($status_dokumen == 'draft') {
                                        $link = url('berkas/temp/' . $berkas);
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
                                         alt="{{$no_dokumen}}">
                                    <h2 class="mt-2 mb-2">Dokumen TTE </h2>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8 order-sm-0 order-lg-0 order-xl-0">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">1. No Dokumen</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$no_dokumen}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">2. Tgl Dokumen</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$tgl_dokumen}}
                                    </label>
                                </div>
                            </div>


                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">3. Hal</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$perihal}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">4. Ditandangani Oleh</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$jenis_ttd->jenis_ttd.' - '.cek_opd($jenis_ttd->id_opd_fk)->nama_opd}}
                                    </label>
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
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
@endpush
