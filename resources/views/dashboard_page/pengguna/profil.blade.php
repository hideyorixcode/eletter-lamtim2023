@extends('mylayouts.app')
@section('title', 'Profil Pengguna')
@push('vendor-css')
    <link rel="stylesheet" href="{{assetku('assets/modules/bootstrap-social/bootstrap-social.css')}}">
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Profil</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item">Profil</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Hi, {{Auth::user()->name}}</h2>
            <p class="section-lead">
                Ubah profil anda di halaman ini.
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
                              action="{{url('dashboard/update-profil')}}">
                            <div class="card-header">
                                <h4>Ubah Profil</h4>
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

                            <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 col-form-label">Nama Lengkap</label>
                                    <div class="col-lg-9 col-xl-9">
                                        <input
                                            class="form-control @error('name') is-invalid @enderror"
                                            name="name" id="name" type="text" value="{{ $name }}"
                                            autofocus/>
                                        @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <!--end::Group-->


                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 col-form-label">Email
                                        Address</label>
                                    <div class="col-lg-9 col-xl-9">

                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><i
                                                        class="fas fa-envelope"></i></span>
                                            </div>
                                            <input type="text"
                                                   class="form-control  @error('email') is-invalid @enderror"
                                                   name="email" value="{{ $email }}"/>
                                            @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!--end::Group-->

                                <!--begin::Group-->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 col-form-label">Username</label>
                                    <div class="col-lg-9 col-xl-9">
                                        <input
                                            class="form-control @error('username') is-invalid @enderror"
                                            name="username" id="username" type="text" value="{{ $username }}"/>
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
                                    <label
                                        class="col-xl-3 col-lg-3 col-form-label text-left">Avatar</label>
                                    <div class="col-lg-9 col-xl-9">
                                        <div id="image-preview" class="image-preview"

                                             style="background-image: url('{{getAvatar(Auth::user()->avatar)}}'); background-size: cover; background-position: center center;"

                                        >
                                            <label for="image-upload" id="image-label">Ubah Avatar</label>
                                            <input type="file" id="image-upload" name="avatar" accept="image/*">
                                        </div>
                                        @if(Auth::user()->avatar)
                                            <br/>
                                            <input type="checkbox" name="remove_avatar"
                                                   value="{{$avatar}}">
                                            Hapus
                                            Avatar
                                            Ketika Disimpan
                                        @endif
                                        @error('avatar')
                                        <p style="color: red">{{ $message }}</p>
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
    <!-- BEGIN: Page JS-->
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{assetku('assets/jshideyorix/general.js')}}"></script>
    <script src="{{assetku('assets/modules/upload-preview/assets/js/jquery.uploadPreview.min.js')}}"></script>
    <!-- END: Page JS-->
    <script type="text/javascript">
        $(document).ready(function() {
            @if(session('pesan_status'))
            tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
            @endif
            getViewSide();
        });

        $.uploadPreview({
            input_field: "#image-upload",   // Default: .image-upload
            preview_box: "#image-preview",  // Default: .image-preview
            label_field: "#image-label",    // Default: .image-label
            label_default: "Choose File",   // Default: Choose File
            label_selected: "Change File",  // Default: Change File
            no_label: false,                // Default: false
            success_callback: null          // Default: null
        });

        @php
            $segment = Request::segment(1).'/'.Request::segment(2);
        @endphp

        function getViewSide() {
            $(".loaderData").show();
            var urlData = "{{ url('dashboard/side-profil') }}";
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
