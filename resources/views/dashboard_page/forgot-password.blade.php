@extends('mylayouts.authlayout')
@section('title', 'Login Aplikasi')
@push('library-css')
@endpush

@section('content')
    <!--begin::Content-->
    <p class="text-muted">Lupa Password?</p>
    <p class="text-muted">Silahkan input email anda yang teregister
        di sistem, sistem akan mengirimkan link reset password melalui email anda</p>

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
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                   tabindex="1" required
                   autofocus>
            @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>


        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary btn-lg btn-icon icon-right" tabindex="4">
                Kirim
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
