@extends('mylayouts.app')
@section('title', 'Detail Pimpinan / Instansi')
@push('library-css')

@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Detail Pimpinan / Instansi</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{route('perangkat-daerah')}}">Daftar PD</a></div>
                <div class="breadcrumb-item active">Detail PD</div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-sm-8 order-sm-0 order-lg-1 order-xl-1">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">1. Nama Perangkat
                                        Daerah</label>
                                    <label class="col-sm-1 col-lg-1 col-form-label">:</label>
                                    <label class="col-sm-6 col-lg-6 col-form-label font-weight-bolder">
                                        {{$nama_opd}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">2. Alias Perangkat
                                        Daerah</label>
                                    <label class="col-sm-1 col-lg-1 col-form-label">:</label>
                                    <label class="col-sm-6 col-lg-6 col-form-label font-weight-bolder">
                                        {{$alias_opd}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">3. Alamat</label>
                                    <label class="col-sm-1 col-lg-1 col-form-label">:</label>
                                    <label class="col-sm-6 col-lg-6 col-form-label font-weight-bolder">
                                        {{$alamat_opd}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">4. Email</label>
                                    <label class="col-sm-1 col-lg-1 col-form-label">:</label>
                                    <label class="col-sm-6 col-lg-6 col-form-label font-weight-bolder">
                                        {{$email_opd}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">5. No Telepon</label>
                                    <label class="col-sm-1 col-lg-1 col-form-label">:</label>
                                    <label class="col-sm-6 col-lg-6 col-form-label font-weight-bolder">
                                        {{$notelepon_opd}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">6. Status</label>
                                    <label class="col-sm-1 col-lg-1 col-form-label">:</label>
                                    <label class="col-sm-6 col-lg-6 col-form-label font-weight-bolder">
                                        <div class="{{getActive($active)}} text-small font-600-bold"><i
                                                class="fas fa-circle"></i> {{getActiveTeks($active)}}</div>
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">7. Jenis</label>
                                    <label class="col-sm-1 col-lg-1 col-form-label">:</label>
                                    <label class="col-sm-6 col-lg-6 col-form-label font-weight-bolder">
                                        @if($jenis == 'opd')
                                            <div class="badge badge-warning">OPD</div>
                                        @elseif($jenis == 'pimpinan daerah')
                                            <div class="badge badge-primary">PIMPINAN DAERAH</div>
                                        @elseif($jenis == 'sekretariat daerah')
                                            <div class="badge badge-success">SEKRETARIAT DAERAH</div>
                                        @elseif($jenis == 'tu')
                                            <div class="badge badge-dark">TU</div>
                                        @else
                                            -
                                        @endif
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right bg-whitesmoke">

                            <a href="{{route('perangkat-daerah')}}" class="btn btn-secondary mr-2">Kembali</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 order-sm-0 order-lg-0 order-xl-0">
                    <div class="card">
                        <div class="card-body">
                            <div class="empty-state">
                                <img class="img-fluid"
                                     src="{{assetku('assets/img/drawkit/drawkit-full-stack-man-colour.svg')}}"
                                     alt="image">
                                <h2 class="mt-0 mb-2">Data Pimpinan / Instansi</h2>
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <div class="alert-body">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                @endif
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
    <!--begin::Page Scripts(used by this page)-->
    <script type="text/javascript">
        @if(session('pesan_status'))
        tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
        @endif
    </script>
@endpush
