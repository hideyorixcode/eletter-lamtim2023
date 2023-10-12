
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ getSetting('nama_app')  }} - Daftar Pencari Kerja</title>
    <meta name="description" content="Lampung Career Center Portal Lowongan Kerja Lampung" />
    <meta name="keywords" content="lcc,lampung career center,portal lowongan kerja lampung" />
    <meta name="author" content="Pemerintah Provinsi Lampung" />

    <link rel="shortcut icon" href="{{ url('uploads/'.getSetting('favicon'))}}">

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{ assetku('assets_front/css/bootstrap.min.css')}}" type="text/css">

    <!--Material Icon -->
    <link rel="stylesheet" type="text/css" href="{{ assetku('assets_front/css/materialdesignicons.min.css')}}" />

    <link rel="stylesheet" type="text/css" href="{{ assetku('assets_front/css/fontawesome.css')}}" />

    <!-- selectize css -->
    <link rel="stylesheet" type="text/css" href="{{ assetku('assets_front/css/selectize.css')}}" />

    <!-- Custom  Css -->
    <link rel="stylesheet" type="text/css" href="{{ assetku('assets_front/css/style.css')}}" />

</head>

<body>
<!-- Loader -->
<div id="preloader">
    <div id="status">
        <div class="spinner">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>
    </div>
</div>
<!-- Loader -->

<div class="back-to-home rounded d-none d-sm-block">
    <a href="{{route('homepage')}}" class="text-white rounded d-inline-block text-center"><i class="mdi mdi-home"></i></a>
</div>

<!-- Hero Start -->
<section class="vh-100" style="background: url({{ assetku('assets_front/images/bg_hero_lowongan.jpg')}}) center center;background-size:cover;">
    <div class="home-center">
        <div class="home-desc-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="login_page bg-white shadow rounded p-4">
                            <div class="text-center">
                                <h4 class="mb-4">Daftar Pencari Kerja</h4>
                            </div>
                            @if($message = session('message'))
                                <div class="alert alert-{{ $message['color'] }} alert-dismissible fade show" role="alert">
                                    {{ $message['message'] }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <form id="form-daftar" class="login-form" method="post" action="{{url()->current()}}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <div class="form-group position-relative">
                                                <label>Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" placeholder="Email" name="email" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group position-relative">
                                                <label>Username <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" placeholder="Username" name="username" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group position-relative">
                                                <label>Nama <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" placeholder="Nama" name="name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group position-relative">
                                                <label>No. Telepon <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" placeholder="Sebaiknya yang digunakan untuk WA juga" name="pencaker_telpon" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group position-relative">
                                                <label>Password <span class="text-danger">*</span></label>
                                                <input id="password" type="password" name="password" class="form-control" placeholder="Password" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group position-relative">
                                                <label>Confirm Password <span class="text-danger">*</span></label>
                                                <input type="password" id="confirm_pass" name="confirm_pass" class="form-control" placeholder="Confirm Password" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <div class="form-group position-relative">
                                                <label>NIK <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" placeholder="NIK sesuai KTP" name="pencaker_nik" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group position-relative">
                                                <label>Alamat <span class="text-danger">*</span></label>
                                                <textarea type="text" class="form-control" placeholder="Alamat anda" name="pencaker_alamat" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group position-relative">
                                                        <label>Provinsi <span class="text-danger">*</span></label>
                                                        <select required id="provinsi" class="mySelectize" name="pencaker_provinsi">
                                                            <option value="">Pilih Provinsi</option>
                                                            @foreach($prov as $p)
                                                                @if (old('provinsi') == $p->nama)
                                                                    <option data-ids="{{ $p->kode }}" value="{{ $p->kode }}" selected>{{ $p->nama }}</option>
                                                                @else
                                                                    <option data-ids="{{ $p->kode }}" value="{{ $p->kode }}">{{ $p->nama }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group position-relative">
                                                        <label>Kabupaten/Kota <span class="text-danger">*</span></label>
                                                        <select required id="kota_kab" class="form-control" name="pencaker_kabkota">
                                                            <option value="">Pilih Kota</option>
                                                            @if(old('kota_kab'))
                                                                <option value="{{ old('kota_kab') }}" selected>{{ old('kota_kab') }}</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group position-relative">
                                                <label>Agama <span class="text-danger"></span></label>
                                                <select required class="mySelectize" name="pencaker_agama">
                                                    <option value="Islam">Islam</option>
                                                    <option value="Kristen">Kristen</option>
                                                    <option value="Hindu">Hindu</option>
                                                    <option value="Katolik">Katolik</option>
                                                    <option value="Buddha">Buddha</option>
                                                    <option value="Konghucu">Konghucu</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary w-100">Register</button>
                                    </div>
                                    <div class="mx-auto">
                                        <p class="mb-0 mt-3"><small class="text-dark mr-2">Sudah punya akun ?</small> <a href="{{route('login-pengguna')}}" class="text-dark font-weight-bold">Login</a></p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div> <!--end col-->
                </div><!--end row-->
            </div> <!--end container-->
        </div>
    </div>
</section><!--end section-->
<!-- Hero End -->

<!-- javascript -->
<script src="{{ assetku('assets_front/js/jquery.min.js')}}"></script>
<script src="{{ assetku('assets_front/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{ assetku('assets_front/js/jquery.easing.min.js')}}"></script>
<script src="{{ assetku('assets_front/js/plugins.js')}}"></script>

<!-- selectize js -->
<script src="{{ assetku('assets_front/js/selectize.min.js')}}"></script>
<script src="{{ assetku('assets_front/js/jquery.nice-select.min.js')}}"></script>
<script src="{{ assetku('assets_front/js/app.js')}}"></script>

<script>
    $(".mySelectize").selectize({
        create: true,
        sortField: "text",
    });
    $(document).ready(function () {
        $('#provinsi').on('change', function() {
            var id = $('#provinsi').val();
            $.ajax({
                url: '{{ url()->current() }}',
                type: 'GET',
                data: {
                    id: id
                },
                success(res) {
                    var str = '';

                    $.each(res, function(k, v) {
                        str += '<option value="'+v.nama+'">'+v.nama+'</option>';
                      //  console.log(v.nama)
                    });
                    $('#kota_kab')
                        .find('option')
                        .remove()
                        .end()
                        .append(str);
                },
                error(err) {
                    console.log("error select : "+err);
                }
            })
        });

    });


</script>

</body>
</html>
