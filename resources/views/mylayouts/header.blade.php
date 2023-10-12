<div class="navbar-bg bg-success"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>
    </form>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown"><a href="#" data-toggle="dropdown"
                                class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="{{getAvatarThumb(Auth::user()->avatar)}}" class="rounded-circle mr-1">
                <div class="d-sm-none d-lg-inline-block">Hi, {{Auth::user()->name}}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-title">Level : {{Auth::user()->level}}</div>
                <a href="{{route('profil')}}" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> Profil
                </a>
                <a href="{{route('my-logs')}}" class="dropdown-item has-icon">
                    <i class="fas fa-bolt"></i> Log Aktivitas
                </a>
                <a href="{{route('ubah-password')}}" class="dropdown-item has-icon">
                    <i class="fas fa-cog"></i> Ubah Password
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                       class="dropdown-item" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fas fa-sign-out-alt"></i>
                         Logout
                    </a>
                </form>
            </div>
        </li>
    </ul>
</nav>
