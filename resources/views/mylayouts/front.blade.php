<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
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
    <link rel="stylesheet" href="{{assetku('assets/modules/sweetalert/sweetalert2.min.css')}}">
    <link
        href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{assetku('assets/css/styledokumen.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/css/components.css')}}">
    @stack('library-css')
</head>

<body class="layout-3">
<div id="app">
    <div class="main-wrapper container">


        <!-- Main Content -->
        <div class="main-content">


            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-primary text-white-all">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-envelope"></i> {{getSetting('nama_app')}}</a></li>
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-building"></i> {{getSetting('area')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-list"></i> @yield('title')</li>
                </ol>
            </nav>


            @yield('content')
        </div>
        <footer class="main-footer">
            <div class="footer-left">
                Copyright &copy; {{date('Y')}}
                <div class="bullet"></div>
                {{getSetting('judul')}} By <a href="#">{{getSetting('author')}}</a>
            </div>
            <div class="footer-right">
                1.0
            </div>
        </footer>
    </div>
</div>

<!-- General JS Scripts -->
<script src="{{assetku('assets/modules/jquery.min.js')}}"></script>
<script src="{{assetku('assets/modules/popper.js')}}"></script>
<script src="{{assetku('assets/modules/tooltip.js')}}"></script>
<script src="{{assetku('assets/modules/bootstrap/js/bootstrap.min.js')}}"></script>
<script src="{{assetku('assets/modules/nicescroll/jquery.nicescroll.min.js')}}"></script>
<script src="{{assetku('assets/modules/moment.min.js')}}"></script>
<script src="{{assetku('assets/js/stisla.js')}}"></script>
<script src="{{assetku('assets/modules/izitoast/js/iziToast.min.js')}}"></script>
<script src="{{assetku('assets/modules/sweetalert/sweetalert2.min.js')}}"></script>
<!-- JS Libraies -->

<!-- Template JS File -->
<script src="{{assetku('assets/js/scripts.js')}}"></script>
<script src="{{assetku('assets/js/custom.js')}}"></script>

<!-- Page Specific JS File -->
@stack('scripts')
</body>
</html>
