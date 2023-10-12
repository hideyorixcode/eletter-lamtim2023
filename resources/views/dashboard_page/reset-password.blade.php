@extends('mylayouts.authlayout')
@section('title', 'Login Aplikasi')
@push('library-css')
@endpush
<!--begin::Content-->
@section('content')
    <p class="text-muted">Reset Password</p>
    <p class="text-muted">Silahkan input password baru anda untuk login aplikasi</p>

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

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" class="form-control @error('name') is-invalid @enderror" name="email"
                   tabindex="1" readonly value="{{$request->email}}">
        </div>

        <div class="form-group">
            <div class="d-block">
                <label for="password" class="control-label">Password Baru</label>
            </div>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   name="password" tabindex="2" placeholder="Password Baru"
                   required>
            @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group">
            <div class="d-block">
                <label for="password" class="control-label">Konfirmasi Password</label>
            </div>
            <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                   name="password_confirmation" tabindex="2" placeholder="Konfirmasi Password"
                   required>
            @error('password_confirmation')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary btn-lg btn-icon icon-right" tabindex="4">
                Konfirmasi Perubahan
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
