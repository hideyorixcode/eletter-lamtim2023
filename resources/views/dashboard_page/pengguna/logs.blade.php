@extends('mylayouts.app')
@section('title', 'Logs Pengguna')
@push('library-css')
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>Log Aktivitas Pengguna</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item">Log Aktivitas Pengguna</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Hi, {{Auth::user()->name}}</h2>
            <p class="section-lead">
                Log Aktivitas anda
            </p>

            <div class="row mt-sm-4">
                <div class="col-12 col-md-12 col-lg-5">
                    <div id="renderviewSide">
                    </div>
                </div>
                <div class="col-12 col-md-12 col-lg-7">
                    @include('components.loader')
                    <div id="renderviewData">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            getViewData(1);
            getViewSide();
        });

        $(document).on('click', '.pagination a', function (event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            getViewData(page);
        });

        function getViewData(page) {
            var pagenya = page ? page : 1;
            $(".loaderData").show();
            var urlData = "{{ url('dashboard/data-logs') }}";
            $.ajax({
                url: urlData,
                type: "GET",
                data:
                    {
                        page: pagenya,
                    },
                success: function (data) {
                    $('#renderviewData').html(data);
                    $(".loaderData").hide();
                }
            });
        }

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
