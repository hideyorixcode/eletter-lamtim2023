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
    <link rel="stylesheet" href="{{assetku('assets/modules/sweetalert/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/css/styledokumen.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/css/components.css')}}">
    <style>
           .blink_me {
               animation: blinker 1s linear infinite;
           }

           @keyframes blinker {
               25% {
                   opacity: 0;
               }
           }
       </style>
    @stack('library-css')
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
<body>
<!--wrapper-->
<div class="wrapper">
    <!-- BEGIN: Header-->
@include('mylayouts.header')
<!-- END: Header-->

    @if(Auth::user()->level=='admin')
        @include('mylayouts.menuadmin')
    @elseif(Auth::user()->level=='umum')
        @include('mylayouts.menuumum')
    @elseif(Auth::user()->level=='superadmin')
        @include('mylayouts.menu')
    @elseif(Auth::user()->level=='adpim')
           @include('mylayouts.menuadpim')
    @elseif(Auth::user()->level=='penandatangan')
              @include('mylayouts.menupenandatangan')
    @else
        @include('mylayouts.menusespri')
    @endif;


    <!--start page wrapper -->
    <div class="main-content">
        @yield('content')
    </div>
    <!--end page wrapper -->

    <footer class="main-footer">
        <div class="footer-left">
            Hak Cipta &copy; {{date('Y')}}
            <div class="bullet"></div>
            {{getSetting('judul')}} oleh <a href="#">{{getSetting('author')}}</a>
        </div>
        <div class="footer-right">
            Terintegrasi dengan Sertifikat Elektronik yang di terbitkan oleh : <img src="{{url('uploads/logo-bsre.png')}}" width="100px">
        </div>
    </footer>
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
<script src="{{assetku('assets/modules/sweetalert/sweetalert2.min.js')}}"></script>
<!-- JS Libraies -->

<!-- Template JS File -->
<script src="{{assetku('assets/js/scripts.js')}}"></script>
<script src="{{assetku('assets/js/custom.js')}}"></script>

<!-- Page Specific JS File -->
@stack('scripts')
</body>
<!-- END: Body-->
</html>
