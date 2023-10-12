@if(count($listNomor)>0)
    <div class="row">
        @php $no = ($listNomor->currentpage()-1)* $listNomor->perpage() + 1;
            $iterasi = 1;
        @endphp
        @foreach($listNomor as $nomor)
            <div class="col-md-12">
                <div class="card card-danger">
                    <div class="card-header">
                        <h4>{{$nomor->nomor_dokumen}}</h4>
                        <div class="card-header-action">
                            <a class="btn btn-icon btn-info"
                               href="{{url('dashboard/pns/detail-pelaksana/'.Hashids::encode($nomor->id))}}"><i
                                    class="fas fa-user-plus"></i> Data Pegawai</a>
                            <div class="dropdown">
                                <a href="#" data-toggle="dropdown"
                                   class="btn btn-warning dropdown-toggle">Actions</a>
                                <div class="dropdown-menu">
                                    <a href="#" class="dropdown-item has-icon clickable-edit"
                                       data-id="{{Hashids::encode($nomor->id)}}"
                                       data-dokumen_id="{{$nomor->dokumen_id}}"
                                       data-tanggal_dokumen="{{TanggalIndo2($nomor->tanggal_dokumen)}}"
                                       data-nomor_dokumen="{{$nomor->nomor_dokumen}}"
                                       data-tentang_dokumen="{{$nomor->tentang_dokumen}}"
                                       data-opd_id="{{$nomor->opd_id}}"
                                    ><i class="far fa-edit"></i> Ubah</a>
                                    <div class="dropdown-divider"></div>
                                    <a href="javascript:void(0)" class="dropdown-item has-icon text-danger"
                                       onclick="deleteData('{{Hashids::encode($nomor->id)}}')"><i
                                            class="far fa-trash-alt"></i> Hapus</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Instansi
                                <span class="font-weight-bolder">{{$nomor->opd->nama_opd}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tanggal Dokumen
                                <span class="">{{TanggalIndo2($nomor->tanggal_dokumen)}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Jumlah SK Pegawai
                                <span class="badge badge-primary badge-pill">{{$nomor->pns_count}}</span>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
            @php $iterasi++; @endphp
        @endforeach
    </div>
    {{ $listNomor->links() }}
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
