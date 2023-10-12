@extends('mylayouts.app')
@section('title', 'Detail Pengguna')
@push('library-css')
    <link rel="stylesheet" type="text/css" href="{{assetku('assets/css/pages/app-user.min.css')}}">
    <link rel="stylesheet" href="{{ assetku('magnific-popup/magnific-popup.min.css')}}">
@endpush
@section('content')

    <section class="section">
        <div class="section-header">
            <h1>Detail Pengguna</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{route('pengguna')}}">Daftar PD</a></div>
                <div class="breadcrumb-item active">Detail Pengguna</div>
            </div>
        </div>
        <div class="section-body">
            <div class="card card-success">
                <div class="card-body">
                    <li class="media">
                        <a class="image-popup-no-margins" href="{{$avatar}}">
                            <img alt="image" class="mr-3 rounded-circle" width="50" src="{{$thumb}}">
                        </a>
                        <div class="media-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="media-title">{{$name}}</div>
                                    <div class="text-muted">{{$email}}</div>
                                    <div class="text-muted">{{$username}}</div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group row m-0">
                                        <label class="col-4 col-form-label">Level:</label>
                                        <div class="col-8">
                                                                               <span
                                                                                   class="form-control-plaintext font-weight-bolder">{{$level}}</span>
                                        </div>
                                    </div>
                                    <div class="form-group row m-0">
                                        <label class="col-4 col-form-label">Status Akun:</label>
                                        <div class="col-8">
                                                           <span class="form-control-plaintext font-weight-bolder"><div
                                                                   class="{{getActive($active)}} text-small font-600-bold"><i
                                                                       class="fas fa-circle"></i> {{getActiveTeks($active)}}</div></span>
                                        </div>
                                    </div>
                                    <div class="form-group row m-0">
                                        <label class="col-4 col-form-label">Tanggal Akun Dibuat:</label>
                                        <div class="col-8">
                                                                                                                   <span
                                                                                                                       class="form-control-plaintext font-weight-bolder">{{TanggalIndowaktu($created_at)}}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </li>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script src="{{ assetku('magnific-popup/jquery.magnific-popup.min.js')}}"></script>
    <script type="text/javascript">
        initMagnific();

        function initMagnific() {
            $('.image-popup-no-margins').magnificPopup({
                type: 'image',
                closeOnContentClick: true,
                fixedContentPos: true,
                mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
                image: {
                    verticalFit: true
                },
            });
        }
    </script>
@endpush
