@extends('mylayouts.app')
@section('title', 'Form '.ucwords($mode).' Pengguna')
@push('vendor-css')
    <style>
        .select_sm {
            height: 33.22222px !important;
            padding-bottom: 2px !important;
            padding-top: 2px !important;
            padding-right: 2px !important;
            padding-left: 2px !important;
        }
    </style>
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
@endpush
@section('content')

    <section class="section">
        <div class="section-header">
            <h1>{{'Form '.ucwords($mode).' Pengguna'}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{route('pengguna')}}">Daftar Pengguna</a></div>
                <div class="breadcrumb-item active">{{'Form '.ucwords($mode).' Pengguna'}}</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-custom card-transparent">
                        <div class="card-body p-0">
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

                        <!--begin::Card-->
                            <form class="card form" id="form"
                                  method="post"
                                  enctype="multipart/form-data"
                                  action="{{$action}}">
                            {{csrf_field()}}
                            @if($mode=='ubah')
                                {{ method_field('PUT') }}
                            @endif
                            <!--begin::Body-->
                                <div class="card-body p-0">
                                    <div class="row justify-content-center py-8 px-8 py-lg-15 px-lg-10">
                                        <div class="col-xl-12 col-xxl-10">
                                            <!--begin::Wizard Form-->

                                            <div class="row justify-content-center">
                                                <div class="col-xl-9">
                                                    <!--begin::Wizard Step 1-->
                                                    <div class="my-5">
                                                        <h5 class="text-dark font-weight-bold mb-10 mt-5">Data
                                                            Pengguna</h5>
                                                        <!--begin::Group-->
                                                        <div class="form-group row">
                                                            <label class="col-xl-3 col-lg-3 col-form-label">Nama
                                                                Lengkap</label>
                                                            <div class="col-lg-9 col-xl-9">
                                                                <input
                                                                    class="form-control @error('name') is-invalid @enderror"
                                                                    name="name" id="name" type="text"
                                                                    value="{{ $name }}"
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
                                                                                                                    <span
                                                                                                                        class="input-group-text"
                                                                                                                        id="inputGroup-sizing-sm"><i
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
                                                            <label
                                                                class="col-xl-3 col-lg-3 col-form-label text-left">Avatar</label>
                                                            <div class="col-lg-9 col-xl-9">
                                                                <div id="image-preview" class="image-preview"
                                                                     style="background-image: url('{{$avatar}}'); background-size: cover; background-position: center center;">
                                                                    <label for="image-upload" id="image-label">Ubah
                                                                        Avatar</label>
                                                                    <input type="file" id="image-upload" name="avatar"
                                                                           accept="image/*">
                                                                </div>
                                                                @if($avatarText!='blank.png')
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
                                                        <!--end::Group-->
                                                    </div>
                                                    <!--end::Wizard Step 1-->
                                                    <!--begin::Wizard Step 2-->
                                                    <div class="separator separator-dashed my-10"></div>
                                                    <div class="my-5" data-wizard-type="step-content">
                                                        <h5 class="text-dark font-weight-bold mb-10 mt-5">Info Login
                                                            Akun</h5>
                                                        <!--begin::Group-->

                                                        <div class="form-group row">
                                                            <label
                                                                class="col-form-label col-xl-3 col-lg-3">Level</label>
                                                            <div class="col-xl-9 col-lg-9">
                                                                <select
                                                                    class="form-control  @error('level') is-invalid @enderror"
                                                                    name="level" id="level">
                                                                    @if(Auth::user()->level=='superadmin')
                                                                        <option
                                                                            value="superadmin" {{$level == 'superadmin' ? 'selected' : ''}}>
                                                                            Super Admin
                                                                        </option>
                                                                    @endif
                                                                    <option
                                                                        value="adpim" {{$level == 'admin' ? 'selected' : ''}}>
                                                                        Admin Protokol Pimpinan
                                                                    </option>
                                                                    <option
                                                                        value="umum" {{$level == 'umum' ? 'selected' : ''}}>
                                                                        Admin Bagian Umum
                                                                    </option>
                                                                    <option
                                                                        value="sespri" {{$level == 'sespri' ? 'selected' : ''}}>
                                                                        Sespri
                                                                    </option>
                                                                </select>
                                                                @error('level')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <!--begin::Group-->
                                                        <div class="form-group row" @if($level!='sespri') style="display: none" @endif id="opdTugas">
                                                            <label class="col-form-label col-xl-3 col-lg-3">Bertugas
                                                                pada</label>
                                                            <div class="col-xl-9 col-lg-9">
                                                                <select class="select_cari form-control" id="id_opd_fk"
                                                                        name="id_opd_fk">
                                                                    @foreach($listPerangkat as $nama => $value)
                                                                        <option
                                                                            value={{$value}} {{$value==$id_opd_fk ? 'selected' : ''}}>{{$nama}}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('id_opd_fk')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <!--end::Group-->


                                                        <!--begin::Group-->
                                                        <div class="form-group row">
                                                            <label class="col-form-label col-xl-3 col-lg-3">Status
                                                                Akun</label>
                                                            <div class="col-xl-9 col-lg-9">
                                                                <select
                                                                    class="form-control  @error('active') is-invalid @enderror"
                                                                    name="active" id="active">
                                                                    <option
                                                                        value=1 {{$active == 1 ? 'selected' : ''}}>
                                                                        Aktif
                                                                    </option>
                                                                    <option
                                                                        value=0 {{$active == 0 ? 'selected' : ''}}>
                                                                        Non
                                                                        Aktif
                                                                    </option>
                                                                </select>
                                                                @error('active')
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
                                                                class="col-xl-3 col-lg-3 col-form-label">Username</label>
                                                            <div class="col-lg-9 col-xl-9">
                                                                <input
                                                                    class="form-control @error('username') is-invalid @enderror"
                                                                    name="username" id="username" type="text"
                                                                    value="{{ $username }}"/>
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
                                                                class="col-xl-3 col-lg-3 col-form-label">Password</label>
                                                            <div class="col-lg-9 col-xl-9">
                                                                <input
                                                                    class="form-control @error('password') is-invalid @enderror"
                                                                    name="password" id="password" type="password"
                                                                    value="{{ $password }}"/>
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
                                                            <label class="col-xl-3 col-lg-3 col-form-label">Konfirmasi
                                                                Password</label>
                                                            <div class="col-lg-9 col-xl-9">
                                                                <input
                                                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                                                    name="password_confirmation"
                                                                    id="password_confirmation"
                                                                    type="password"
                                                                    value="{{ $password_confirmation }}"/>
                                                                @error('password_confirmation')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <!--end::Group-->
                                                    </div>
                                                    <!--end::Wizard Step 2-->
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!--end::Body-->
                                <div class="card-footer text-right">
                                    {{--                    <button type="reset" class="btn btn-secondary mr-2">Reset Form</button>--}}
                                    <button type="submit" class="btn btn-primary mr-2"><i class="mr-50 fa fa-save"></i>
                                        Submit
                                    </button>

                                </div>
                            </form>
                        </div>
                        <!--end::Card-->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
    <script src="{{assetku('assets/modules/upload-preview/assets/js/jquery.uploadPreview.min.js')}}"></script>
    <script src="{{assetku('assets/modules/select2/dist/js/select2.full.min.js')}}"></script>
    <!--begin::Page Scripts(used by this page)-->
    <script type="text/javascript">
        @if(session('pesan_status'))
        tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
        @endif

        $.uploadPreview({
            input_field: "#image-upload",   // Default: .image-upload
            preview_box: "#image-preview",  // Default: .image-preview
            label_field: "#image-label",    // Default: .image-label
            label_default: "Choose File",   // Default: Choose File
            label_selected: "Change File",  // Default: Change File
            no_label: false,                // Default: false
            success_callback: null          // Default: null
        });

        function previewImg() {
            const logo = document.querySelector('#anggota_avatar');
            //const logoLabel = document.querySelector('.custom-file-label');
            const logoPreview = document.querySelector('.img-preview');

            //logoLabel.textContent = logo.files[0].name;

            const fileLogo = new FileReader();
            fileLogo.readAsDataURL(logo.files[0]);

            fileLogo.onload = function (e) {
                logoPreview.src = e.target.result;
            }
        }

        function bukatutupOPD() {
            value_level = $("#level").val();
            if (value_level == 'sespri') {
                $("#opdTugas").show();
            } else {
                $("#opdTugas").hide();
            }
        }

        $('#level').on('change', function () {
            bukatutupOPD();
        });

        <!--end::Page Scripts-->
    </script>
@endpush
