@extends('mylayouts.front')
@section('title', 'Detail Signature QR')
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

                                <img class="img-fluid"
                                     src="{{url('signatureqr/'.$qrcode)}}"
                                     alt="{{$qrcode}}">
                                <h2 class="mt-2 mb-2">Signature QR</h2>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8 order-sm-0 order-lg-0 order-xl-0">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">1. Judul</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$judul}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">2. Tgl</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$tgl}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">3. Pejabat / Perangkat
                                        Daerah</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$nama_opd}}
                                    </label>
                                </div>
                            </div>
                            @if($nomor_surat)
                                <hr class="mb-2">
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-5 col-lg-5 col-form-label">4. Nomor Surat</label>

                                        <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                            {{$nomor_surat}}
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
