<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="javascript:void(0)"><img src="{{url('uploads/'.getSetting('logo'))}}" width="25px">
                 {{getSetting('nama_app')}}</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#"><img src="{{url('uploads/'.getSetting('logo'))}}" width="25px"> </a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Modul</li>
            <li class="{{activeSegment('dashboard')}}"><a class="nav-link" href="{{route('dashboard')}}"><i
                        class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li class="{{activeMenu('dashboard/surat-masuk')}}"><a class="nav-link"
                                                                   href="{{url('dashboard/surat-masuk')}}"><i
                        class="fas fa-inbox"></i> <span>Surat Masuk</span></a></li>

            <li class="{{activeMenu('dashboard/surat-masuk-instansi')}}"><a class="nav-link"
                                                                            href="{{url('dashboard/surat-masuk-instansi')}}"><i
                        class="fas fa-file"></i> <span>Surat Masuk Instansi</span></a></li>
            <li class="{{activeMenu('dashboard/surat-keluar')}}"><a class="nav-link"
                                                                    href="{{url('dashboard/surat-keluar')}}"><i
                        class="fas fa-paper-plane"></i> <span>Surat Keluar</span></a></li>
            <li class="{{activeMenu('dashboard/surat-keluar-tte')}}"><a class="nav-link"
                                                                        href="{{url('dashboard/surat-keluar-tte')}}"><i
                        class="fas fa-file-signature"></i> <span>TTE Surat Keluar</span></a></li>
            <li class="{{activeMenu('dashboard/dokumen-tte')}}"><a class="nav-link"
                                                                   href="{{url('dashboard/dokumen-tte')}}"><i
                        class="fas fa-file-code"></i> <span>Dokumen TTE</span></a></li>
            <li class="{{activeMenu('dashboard/verifikasi-tte')}}"><a class="nav-link"
                                                                              href="{{url('dashboard/verifikasi-tte')}}"><i
                                   class="fas fa-check-square"></i> <span>Verifikasi TTE</span></a></li>

        </ul>
    </aside>
</div>
