@extends('mylayouts.front')
@section('title', 'Data PNS SK Jabatan Pelaksana '.$dataMaster->Nama)
@push('vendor-css')

@endpush
@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-md-8 mt-2">
                    <div class="card">
                        <div class="card-header">
                            <h4>Data PNS </h4>
                        </div>

                        <div class="card-body">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">No Urut</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$dataMaster->urut_peg}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">Nama</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$dataMaster->nama}}
                                    </label>
                                </div>
                            </div>

                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">NIP</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$dataMaster->nip}}
                                    </label>
                                </div>
                            </div>


                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">Tanggal Lahir</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{TanggalIndo2($dataMaster->tgl_lahir)}}
                                    </label>
                                </div>
                            </div>

                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">Pendidikan</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$dataMaster->pendidikan}}
                                    </label>
                                </div>
                            </div>

                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">Pangkat/Gol.</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$dataMaster->pangkat_gol}}
                                    </label>
                                </div>
                            </div>

                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">Jabatan</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        @if($dataMaster->jabatan_lama == $dataMaster->jabatan_baru)
                                            {{strtoupper($dataMaster->jabatan_baru)}}
                                        @else
                                            {{strtoupper($dataMaster->jabatan_lama)}} <i class="fas fa-arrow-alt-circle-right"
                                                                           style="color: red"></i> {{strtoupper($dataMaster->jabatan_baru)}}
                                        @endif
                                    </label>
                                </div>
                            </div>

                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">Kelas Jabatan</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        @if($dataMaster->kelas_lama == $dataMaster->kelas_baru)
                                            {{$dataMaster->kelas_baru}}
                                        @else
                                            {{$dataMaster->kelas_lama}} <i class="fas fa-arrow-alt-circle-right"
                                                                           style="color: red"></i> {{$dataMaster->kelas_baru}}
                                        @endif
                                    </label>
                                </div>
                            </div>

                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">Unker</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        @if($dataMaster->unker_lama == $dataMaster->unker_baru)
                                            {{strtoupper($dataMaster->unker_baru)}}
                                        @else
                                            {{strtoupper($dataMaster->unker_lama)}} <i class="fas fa-arrow-alt-circle-right"
                                                                           style="color: red"></i> {{strtoupper($dataMaster->unker_baru)}}
                                        @endif
                                    </label>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
                <div class="col-md-4 mt-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">No SK</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$dataMaster->dokumen->nomor_dokumen}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">Tanggal SK</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{TanggalIndo2($dataMaster->dokumen->tanggal_dokumen)}}
                                    </label>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">Tentang</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$dataMaster->dokumen->tentang_dokumen}} DI LINGKUNGAN {{$dataMaster->pns_opd}}
                                    </label>
                                </div>
                            </div>

                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-lg-5 col-form-label">dto</label>

                                    <label class="col-sm-7 col-lg-7 col-form-label font-weight-bolder">
                                        {{$dataMaster->dokumen->master->jenisttd->jenis_ttd.' - '.cek_opd($dataMaster->dokumen->master->jenisttd->id_opd_fk)->nama_opd}}
                                    </label>
                                </div>
                            </div>

                            <hr class="mb-2">
                            <div class="col-sm-12">
                                <div class="row mb-3">
                                    <a target="_blank"
                                       href="{{ url()->current() }}/download"
                                       class="btn btn-primary btn-block"><i class="fa fa-download"></i> Download</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')

    <!--end::Page Vendors-->
    <!--begin::Page Scripts(used by this page)-->
    <script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
    <script>
    </script>
@endpush
