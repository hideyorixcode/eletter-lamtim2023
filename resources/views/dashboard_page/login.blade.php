@extends('mylayouts.authlayout')
@section('title', 'Login Aplikasi')
@push('library-css')
@endpush
<!--begin::Content-->
@section('content')
    <!-- Login-->

    <p class="text-muted">Form Login Aplikasi</p>

    @if (session('status'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="alert-body">
                {{session('status')}}
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

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

    <form method="POST" action="{{ route('login') }}" autocomplete="off">
        @csrf
        <div class="form-group">
            <label for="email">Username</label>
            <input id="username" type="text" class="form-control @error('name') is-invalid @enderror" name="username"
                   tabindex="1" required
                   autofocus autocomplete="off">
            @error('username')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <div class="d-block">
                <label for="password" class="control-label">Password</label>
            </div>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   name="password" tabindex="2"
                   required autocomplete="off">
            @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group text-right">
            <a href="{{url('forgot-password')}}" class="float-left mt-3">
                Forgot Password?
            </a>
            <button type="submit" class="btn btn-primary btn-lg btn-icon icon-right" tabindex="4">
                Login
            </button>
        </div>


    </form>

@endsection
@push('scripts')
    <!--begin::Page Scripts(used by this page)-->
    <script>

    </script>
    <!--end::Page Scripts-->
@endpush
