<!DOCTYPE html>
<html lang="en">
<!-- BEGIN: Head-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{getSetting('deskripsi')}}">
    <meta name="keywords" content="{{getSetting('keyword')}}">
    <meta name="author" content="{{getSetting('author')}}">
    <title>{{ getSetting('nama_app')  }} - @yield('title')</title>
    <!--favicon-->
    <link rel="icon" type="image/x-icon" href="{{url('uploads/'.getSetting('favicon'))}}">
    <!--plugins-->
    <link rel="stylesheet" href="{{assetku('assets/modules/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/fontawesome/css/all.min.css')}}">
    @stack('vendor-css')
    <link rel="stylesheet" href="{{assetku('assets/modules/izitoast/css/iziToast.min.css')}}">
{{--    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">--}}
    <link rel="stylesheet" href="{{assetku('assets/css/styledokumen.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/css/components.css')}}">
    @stack('library-css')
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
<body>
<!--wrapper-->
<div id="app">
    <section class="section">
        <div class="d-flex flex-wrap align-items-stretch">
            <div class="col-lg-4 col-md-6 col-12 order-lg-1 min-vh-100 order-2 bg-white">
                <div class="p-4 m-3">
                    <img src="{{url('uploads/'.getSetting('logo'))}}" alt="logo" width="80"
                         class="shadow-light rounded-circle mb-5 mt-2">
                    <h4 class="text-dark font-weight-normal"><span
                            class="font-weight-bold">{{getSetting('nama_app')}}</span>
                    </h4>
                    @yield('content')

                    <div class="text-center mt-5 text-small">
                        Hak Cipta &copy; {{getSetting('author')}}
                    </div>
                    <div class="text-center mt-1 text-small">
                        Terintegrasi dengan Sertifikat Elektronik yang di terbitkan oleh : <img src="{{url('uploads/logo-bsre.png')}}" width="100px">
                    </div>

                </div>
            </div>
            <div
                class="col-lg-8 col-12 order-lg-2 order-1 min-vh-100 background-walk-y position-relative overlay-gradient-bottom"
                data-background="{{url('uploads/'.getSetting('banner_login'))}}">
                <div class="absolute-bottom-left index-2">
                    <div class="text-light p-5 pb-2">
                        <div class="mb-5 pb-3">
                            <h1 class="mb-2 display-4 font-weight-bold">{{getSetting('area')}}</h1>
                            <h5 class="font-weight-normal text-muted-transparent">{{getSetting('alamat')}}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!--end wrapper-->
<!-- General JS Scripts -->
<script src="{{assetku('assets/modules/jquery.min.js')}}"></script>
<script src="{{assetku('assets/modules/popper.js')}}"></script>
<script src="{{assetku('assets/modules/tooltip.js')}}"></script>
<script src="{{assetku('assets/modules/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="{{assetku('assets/modules/nicescroll/jquery.nicescroll.min.js')}}"></script>
<script src="{{assetku('assets/modules/moment.min.js')}}"></script>
<script src="{{assetku('assets/js/stisla.js')}}"></script>
<script src="{{assetku('assets/modules/izitoast/js/iziToast.min.js')}}"></script>
<!-- JS Libraies -->

<!-- Template JS File -->
<script src="{{assetku('assets/js/scripts.js')}}"></script>
<script src="{{assetku('assets/js/custom.js')}}"></script>

<!-- Page Specific JS File -->
@stack('scripts')
</body>
<!-- END: Body-->
</html>
