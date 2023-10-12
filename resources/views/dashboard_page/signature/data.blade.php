@if(count($listQR)>0)
    @php $no = ($listQR->currentpage()-1)* $listQR->perpage() + 1;@endphp
    <div class="row">
        @foreach($listQR as $dataQR)
            @php
                $avatar = $dataQR->qrcode ? url('signatureqr/'.$dataQR->qrcode) :url('uploads/blank.png');
            @endphp
            <div class="col-12 col-sm-6 col-md-6 col-lg-3 d-flex align-items-stretch">
                <article class="article article-style-b">
                    <div class="article-header">
                        <a class="image-popup-no-margins" href="{{$avatar}}">
                            <div class="article-image" data-background="{{$avatar}}"
                                 style="background-image: url({{$avatar}});">
                            </div>
                        </a>
                    </div>
                    <div class="article-details">
                        <div class="article-title">
                            <h2><a href="#">{{$dataQR->nama_opd}} <input type="checkbox"
                                                                         @if(in_array(Hashids::encode($dataQR->id), $arrayList)) checked
                                                                         @endif onchange="checkBulk()"
                                                                         value="{{Hashids::encode($dataQR->id)}}"
                                                                         class="data-check"></a></h2>
                        </div>
                        <p>Tgl Surat : {{tanggalIndo($dataQR->tgl)}}</p>
                        <p>{{$dataQR->judul}}</p>
                        <div class="article-cta">

                            <div class="dropdown d-inline show">
                                <button class="btn btn-success btn-sm dropdown-toggle" type="button"
                                        id="dropdownMenuButton2"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    Pilih Aksi
                                </button>
                                <div class="dropdown-menu" x-placement="bottom-start"
                                     style="position: absolute; transform: translate3d(0px, 27px, 0px); top: 0px; left: 0px; will-change: transform;">
                                    <a class="dropdown-item has-icon" href="{{url('signatureqr/'.$dataQR->qrcode)}}"
                                       target="_blank"><i class="fa fa-download"></i> Download</a>
                                    <a class="dropdown-item has-icon"
                                       href="{{url('dashboard/signature-qr/print/'.Hashids::encode($dataQR->id))}}"
                                       target="_blank"><i class="fa fa-print"></i> Print</a>
                                    <a class="dropdown-item has-icon"
                                       href="{{url('signature-qr/'.Hashids::encode($dataQR->id))}}"
                                       target="_blank"><i
                                            class="fa fa-eye"></i> Detail</a>
                                    <a class="dropdown-item has-icon"
                                       href="{{url('dashboard/signature-qr/edit/'.Hashids::encode($dataQR->id))}}"><i
                                            class="fa fa-edit"></i> Edit</a>
                                    <a class="dropdown-item has-icon" href="javascript:void(0)"
                                       onclick="deleteData('{{Hashids::encode($dataQR->id)}}')"><i
                                            class="fa fa-trash"></i> Hapus</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
            @php $no++; @endphp
        @endforeach
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
    {{ $listQR->links() }}
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
