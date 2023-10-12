<div class="card author-box card-primary">
    <div class="card-body">
        <div class="author-box-left">
            <img alt="image" src="{{getAvatar(Auth::user()->avatar)}}" class="rounded-circle author-box-picture">
            <div class="clearfix"></div>
            <a href="#" class="btn btn-primary mt-3 follow-btn">{{Auth::user()->level}} {{Auth::user()->level=='sespri' ? cek_opd(Auth::user()->id_opd_fk)->nama_opd : ''}} </a>
        </div>
        <div class="author-box-details">
            <div class="author-box-name">
                <a href="#">{{Auth::user()->name}}</a>
            </div>
            <div class="author-box-job">{{Auth::user()->username}}</div>
            <div class="author-box-description">
                <div class="form-group row my-2">
                    <label class="col-4 col-form-label">Email:</label>
                    <div class="col-8"><span
                            class="form-control-plaintext font-weight-bolder">{{Auth::user()->email}}</span>
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label class="col-4 col-form-label">Status:</label>
                    <div class="col-8"><span
                            class="form-control-plaintext font-weight-bolder"><div class="{{getActive(Auth::user()->active)}} text-small font-600-bold"><i class="fas fa-circle"></i> {{getActiveTeks(Auth::user()->active)}}</div></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item"><a href="{{route('profil')}}"
                                    class="nav-link {{ $segment=='dashboard/profil' ? 'active' : '' }}">Profil</a>
            </li>
            <li class="nav-item"><a href="{{route('ubah-password')}}"
                                    class="nav-link {{ $segment=='dashboard/ubah-password' ? 'active' : '' }}">Ubah
                    Password</a></li>
            <li class="nav-item"><a href="{{route('my-logs')}}"
                                    class="nav-link {{ $segment=='dashboard/my-logs' ? 'active' : '' }}">Log
                    Saya</a>
            </li>
        </ul>
    </div>
</div>
