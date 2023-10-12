@if(count($listPengguna)>0)
    @php $no = ($listPengguna->currentpage()-1)* $listPengguna->perpage() + 1;@endphp
    <div class="card card-success">
        <div class="card-body">
            <ul class="list-unstyled user-progress list-unstyled-border list-unstyled-noborder">
                @foreach($listPengguna as $dataPengguna)
                    @php
                        $activePengguna = $dataPengguna->active;
                        $warnaActive = $activePengguna==1 ? '<span class="badge badge-pill badge-light-success"> AKTIF </span>' : '<span class="badge badge-pill badge-light-danger"> NON AKTIF </span>';
                        $avatar = $dataPengguna->avatar ? url('uploads/'.$dataPengguna->avatar) :url('uploads/blank.png');
                        $thumb = $dataPengguna->avatar ? url('uploads/thumbnail/'.$dataPengguna->avatar) :url('uploads/blank.png');
                    @endphp

                    <li class="media">
                        <a class="image-popup-no-margins" href="{{$avatar}}">
                            <img alt="image" class="mr-3 rounded-circle" width="50" src="{{$thumb}}">
                        </a>
                        <div class="media-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="media-title">{{$dataPengguna->name}}</div>
                                    <div class="text-muted">{{$dataPengguna->email}}</div>
                                    <div class="text-muted">{{$dataPengguna->username}}</div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group row m-0">
                                        <label class="col-4 col-form-label">Level:</label>
                                        <div class="col-8">
                                            <span
                                                class="form-control-plaintext font-weight-bolder">{{$dataPengguna->level}}</span>
                                        </div>
                                    </div>
                                    @if($dataPengguna->id_opd_fk!='')
                                        <div class="form-group row m-0">
                                            <label class="col-4 col-form-label">Bertugas di :</label>
                                            <div class="col-8">
                                                <span
                                                    class="form-control-plaintext font-weight-bolder">{{$dataPengguna->nama_opd}}</span>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="form-group row m-0">
                                        <label class="col-4 col-form-label">Status Akun:</label>
                                        <div class="col-8">
                                                <span class="form-control-plaintext font-weight-bolder"><div
                                                        class="{{getActive($dataPengguna->active)}} text-small font-600-bold"><i
                                                            class="fas fa-circle"></i> {{getActiveTeks($dataPengguna->active)}}</div></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="">
                            <div class="btn-group" role="group" aria-label="First group">
                                <a href="{{url('dashboard/pengguna/show/'.Hashids::encode($dataPengguna->id))}}"
                                   class="btn btn-sm btn-outline-warning btn-icon"
                                   title="Show"><i class="fa fa-eye"></i></a>
                                <a href="{{url('dashboard/pengguna/edit/'.Hashids::encode($dataPengguna->id))}}"
                                   class="btn btn-sm btn-outline-success btn-icon"
                                   title="Edit"><i class="fa fa-edit"></i></a>
                                <a href="javascript:void(0)"
                                   onclick="deleteData('{{Hashids::encode($dataPengguna->id)}}')"
                                   class="btn btn-sm btn-outline-danger btn-icon" title="Hapus"><i
                                        class="fa fa-trash"></i></a>
                                <a href="javascript:void(0)"
                                   class="btn btn-sm btn-outline-primary btn-icon"
                                   title="Pilih"><input
                                        type="checkbox"
                                        @if(in_array(Hashids::encode($dataPengguna->id), $arrayList)) checked
                                        @endif onchange="checkBulk()"
                                        value="{{Hashids::encode($dataPengguna->id)}}"
                                        class="data-check"></a>
                            </div>
                        </div>
                    </li>

                    @php $no++; @endphp
                @endforeach
            </ul>
        </div>
    </div>
    <div class="card card-dark mb-2">
        <div class="card-body mb-0">
            <div class="row">
                <div class="col-md-8">
                    <div class="dropdown d-inline">
                        <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton2"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Pilih Opsi
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item has-icon" href="javascript:bulkDelete()"><i
                                    class="fa fa-trash text-danger"></i> Hapus yang dipilih</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input"
                                       id="check-all">
                                <label class="custom-control-label" for="check-all">Seluruh
                                    Data</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control selectrowCount" name="page_count"
                                    id="page_count" style="border-right-width: 0px;
                     border-bottom-width: 0px;
                     padding-top: 0px;
                     padding-right: 0px;
                     padding-left: 0px;
                     padding-bottom: 0px;
                     height: 22.22222px;
                     width: 72.22222px;">
                                <option disabled>Jumlah Tampil</option>
                                @foreach($paginateList as $value)
                                    <option
                                        value={{$value}} {{$value==$page_count ? 'selected' : ''}}>{{$value == -1 ? 'ALL' : $value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ $listPengguna->links() }}
@else
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <div class="alert-body">
            TIDAK DITEMUKAN DATA
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
@endif
