@extends('mylayouts.app')
@section('title', 'Ubah Password')
@push('vendor-css')
@endpush

@section('content')

    <section class="section">
        <div class="section-header">
            <h1>Ubah Password</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item">Ubah Password</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Hi, {{Auth::user()->name}}</h2>
            <p class="section-lead">
                Ubah password anda di halaman ini.
            </p>

            <div class="row mt-sm-4">
                <div class="col-12 col-md-12 col-lg-5">
                    @include('components.loader')
                    <div id="renderviewSide">

                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-7">
                    <div class="card">
                        <form id="form" method="post"
                              enctype="multipart/form-data"
                              action="{{url('dashboard/update-password')}}">
                            <div class="card-header">
                                <h4>Ubah Password</h4>
                            </div>
                            <div class="card-body">
                                {{csrf_field()}}
                                {{ method_field('PUT') }}

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

                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 col-form-label">Username</label>
                                    <div class="col-lg-9 col-xl-9">
                                        <input
                                            class="form-control @error('username') is-invalid @enderror"
                                            name="username" id="username" type="text" readonly
                                            value="{{ Auth::user()->username }}"/>
                                        @error('username')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 col-form-label">Password Lama</label>
                                    <div class="col-lg-9 col-xl-9">
                                        <input
                                            class="form-control @error('password_old') is-invalid @enderror"
                                            name="password_old" id="password_old" type="password"/>
                                        @error('password_old')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 col-form-label">Password Baru</label>
                                    <div class="col-lg-9 col-xl-9">
                                        <input
                                            class="form-control @error('password') is-invalid @enderror"
                                            name="password" id="password" type="password"/>
                                        @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 col-form-label">Konfirmasi Password</label>
                                    <div class="col-lg-9 col-xl-9">
                                        <input
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            name="password_confirmation" id="password_confirmation"
                                            type="password"/>
                                        @error('password_confirmation')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>


                            </div>
                            <div class="card-footer text-right">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Simpan
                                    Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{assetku('assets/jshideyorix/general.js')}}"></script>
    <!-- END: Page JS-->
    <script type="text/javascript">
        $(document).ready(function() {
            @if(session('pesan_status'))
            tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
            @endif
            getViewSide();
        });

        @php
            $segment = Request::segment(1).'/'.Request::segment(2);
        @endphp

        function getViewSide() {
            $(".loaderData").show();
            var urlData;

            urlData = "{{ url('dashboard/side-profil') }}";

            $.ajax({
                url: urlData,
                type: "GET",
                data:
                    {
                        segment: '{{$segment}}',
                    },
                success: function (data) {
                    $('#renderviewSide').html(data);
                    $(".loaderData").hide();
                }
            });
        }

    </script>
@endpush
