
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ getSetting('nama_app')  }} - Login Aplikasi</title>
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
<section class="vh-100" style="background: url({{ assetku('assets_front/images/bg_hero_home.jpg')}}) center center;background-size:cover;">

    <div class="home-center">
        <div class="home-desc-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6">
                        @if($message = session('message'))
                        <div class="alert alert-{{ $message['color'] }} alert-dismissible fade show" role="alert">
                            {{ $message['message'] }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                          </div>
                        @endif
                        <div class="login-page bg-white shadow rounded p-4">
                            <div class="text-center">
                                <h4 class="mb-4">Login</h4>
                            </div>
                            <form class="login-form" action="{{ url()->current() }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group position-relative">
                                            <label>Username <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('username') is-invalid @enderror" placeholder="Username anda" name="username" required="" autocomplete="off">
                                            @error('username')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group position-relative">
                                            <label>Password <span class="text-danger">*</span></label>
                                            <input type="password" class="@error('password') is-invalid @enderror form-control" placeholder="Password" name="password" required="" autocomplete="off">
                                            @error('username')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-12 mb-0">
                                        <button class="btn btn-primary w-100">Sign in</button>
                                    </div>
                                    @if($message)
                                    <a href="{{ url('/forgot-password') }}" class="ml-auto mr-auto mt-2">Klik disini untuk <strong class="text-danger font-weight-bold">Lupa password!</strong></a>
                                    @endif

                                    <div class="col-12 text-center">
                                        <p class="mb-0 mt-3"><small class="text-dark mr-2">Belum punya akun ?</small>
                                            <br> <a href="{{route('register-pencaker')}}" class="text-success font-weight-bold">Daftar sebagai Pencari Kerja</a>
                                            <br>
                                            <small>atau</small> <br>
                                            <a href="{{url('/daftar/perusahaan')}}" class="text-primary font-weight-bold">Daftar sebagai Penyedia Kerja</a>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div><!---->
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
</body>
</html>
