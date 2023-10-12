@extends('mylayouts.app')
@section('title', 'Form '.ucwords($mode).' Pimpinan / Instansi')
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
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{'Form '.ucwords($mode).' Pimpinan / Instansi'}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{route('perangkat-daerah')}}">Daftar PD</a></div>
                <div class="breadcrumb-item active">{{'Form '.ucwords($mode).' PD'}}</div>
            </div>
        </div>
        <div class="section-body">
            <form id="form" name="form" role="form" action="{{$action}}"
                  enctype="multipart/form-data" method="post">
                <div class="row">

                    <div class="col-sm-8 order-sm-0 order-lg-1 order-xl-1">
                        <div class="card">

                            {{csrf_field()}}
                            @if($mode=='ubah')
                                {{ method_field('PUT') }}
                            @endif
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Nama</label>
                                        <div class="col-sm-9 col-lg-9">
                                            <div class="input-group">
                                 <span class="input-group-prepend">
                                 <label class="input-group-text">
                                 <i class="fa fa-building"></i></label>
                                 </span>
                                                <input class="form-control @error('nama_opd') is-invalid @enderror"
                                                       placeholder="Contoh : Badan Perencanaan Daerah"
                                                       required="required" name="nama_opd" id="nama_opd"
                                                       type="text" value="{{$nama_opd}}">
                                            </div>
                                            @error('nama_opd')
                                            <div class="invalid-feedback" id="error_nama_opd">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Alias</label>
                                        <div class="col-sm-9 col-lg-9">
                                            <div class="input-group">
                                 <span class="input-group-prepend">
                                 <label class="input-group-text">
                                 <i class="fa fa-chalkboard-teacher"></i></label>
                                 </span>
                                                <input class="form-control @error('alias_opd') is-invalid @enderror"
                                                       placeholder="Contoh : Bappeda"
                                                       required="required" name="alias_opd" id="alias_opd"
                                                       type="text"
                                                       value="{{$alias_opd}}">
                                            </div>
                                            @error('alias_opd')
                                            <div class="invalid-feedback" id="error_alias_opd">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Alamat</label>
                                        <div class="col-sm-9 col-lg-9">
                                            <div class="input-group">
                                 <span class="input-group-prepend">
                                 <label class="input-group-text">
                                 <i class="fa fa-address-book"></i></label>
                                 </span>
                                                <input
                                                    class="form-control @error('alamat_opd') is-invalid @enderror"
                                                    placeholder="Contoh : Komplek Perkantoran Pemda Lampung Timur"
                                                    name="alamat_opd" id="alamat_opd"
                                                    type="text" value="{{$alamat_opd}}">
                                            </div>
                                            @error('alamat_opd')
                                            <div class="invalid-feedback" id="error_alamat_opd">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Email</label>
                                        <div class="col-sm-9 col-lg-9">
                                            <div class="input-group">
                                 <span class="input-group-prepend">
                                 <label class="input-group-text">
                                 <i class="fa fa-envelope"></i></label>
                                 </span>
                                                <input class="form-control @error('email_opd') is-invalid @enderror"
                                                       placeholder="Contoh : bappeda@lampungtimurkab.go.id"
                                                       name="email_opd" id="email_opd"
                                                       type="email"
                                                       value="{{$email_opd}}">
                                            </div>
                                            @error('email_opd')
                                            <div class="invalid-feedback" id="error_email_opd">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">No Telepon</label>
                                        <div class="col-sm-9 col-lg-9">
                                            <div class="input-group">
                                 <span class="input-group-prepend">
                                 <label class="input-group-text">
                                 <i class="fa fa-phone"></i></label>
                                 </span>
                                                <input
                                                    class="form-control @error('notelepon_opd') is-invalid @enderror"
                                                    placeholder="Contoh : (0721) 485458"
                                                    name="notelepon_opd" id="notelepon_opd"
                                                    type="text" onkeypress="return check_int(event)" maxlength="14"
                                                    value="{{$notelepon_opd}}">
                                            </div>
                                            @error('notelepon_opd')
                                            <div class="invalid-feedback" id="error_notelepon_opd">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Jenis</label>
                                        <div class="col-sm-9 col-lg-9">
                                            <div class="input-group">
                                                          <span class="input-group-prepend">
                                                          <label class="input-group-text">
                                                          <i class="fa fa-unlock"></i></label>
                                                          </span>
                                                <select name="jenis"
                                                        class="form-control @error('jenis') is-invalid @enderror">
                                                    <option value="opd" {{$jenis=='opd' ? 'selected' : ''}}>OPD
                                                    </option>
                                                    <option
                                                        value="pimpinan daerah" {{$jenis=='pimpinan daerah' ? 'selected' : ''}}>
                                                        Pimpinan Daerah
                                                    </option>
                                                    <option
                                                        value="sekretariat daerah" {{$jenis=='sekretariat daerah' ? 'selected' : ''}}>
                                                        Sekretariat Daerah
                                                    </option>
                                                    <option value="tu" {{$jenis=='tu' ? 'selected' : ''}}>TU
                                                    </option>
                                                </select>
                                            </div>
                                            @error('jenis')
                                            <div class="invalid-feedback" id="error_jenis">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Status</label>
                                        <div class="col-sm-9 col-lg-9">
                                            <div class="input-group">
                                 <span class="input-group-prepend">
                                 <label class="input-group-text">
                                 <i class="fa fa-unlock"></i></label>
                                 </span>
                                                <select name="active"
                                                        class="form-control @error('active') is-invalid @enderror">
                                                    <option value="1" {{$active==1 ? 'selected' : ''}}>Aktif
                                                    </option>
                                                    <option value="0" {{$active==0 ? 'selected' : ''}}>Tidak Aktif
                                                    </option>
                                                </select>
                                            </div>
                                            @error('active')
                                            <div class="invalid-feedback" id="error_active">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right bg-whitesmoke">
                                @if($mode=='tambah')
                                    <button type="reset" class="btn btn-secondary mr-2">Reset Form</button>
                                @endif
                                <button type="submit" class="btn btn-primary mr-2"><i class="mr-50 fa fa-save"></i>
                                    @if($mode=='ubah') Simpan Perubahan @else Submit @endif
                                </button>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-4 order-sm-0 order-lg-0 order-xl-0">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="empty-state">
                                            <img class="img-fluid"
                                                 src="{{assetku('assets/img/drawkit/drawkit-full-stack-man-colour.svg')}}"
                                                 alt="image">

                                            @if (count($errors) > 0)
                                                <div class="alert alert-danger alert-dismissible fade show"
                                                     role="alert">
                                                    <div class="alert-body">
                                                        <ul>
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                    <button type="button" class="close" data-dismiss="alert"
                                                            aria-label="Close">
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

                </div>
            </form>
        </div>
    </section>
@endsection
@push('scripts')
    <script src="{{assetku('assets/modules/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
    <!--begin::Page Scripts(used by this page)-->
    <script type="text/javascript">
        @if(session('pesan_status'))
        tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
        @endif


        function getnamaunitkerja() {
            var uk = $('#T_KUnker').val();
            var explode = uk.split("|");
            $('#nama_opd').val(explode[1]);
        }

        $('#T_KUnker').change(function () {
            getnamaunitkerja();
        });
    </script>
@endpush
