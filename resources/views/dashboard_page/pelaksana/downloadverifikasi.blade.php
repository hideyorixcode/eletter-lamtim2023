@extends('mylayouts.front')
@section('title', 'Data PNS SK Jabatan Pelaksana '.$dataMaster->Nama)
@push('vendor-css')

@endpush
@section('content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                @if($kondisi == 'salah')
                    <div class="col-md-12 mt-2">
                        <div class="card">
                            <div class="card-body">
                                <div class="alert alert-{{$status}} mb-2" role="alert"> {{$pesan}} </div>
                                <form method="get"
                                      action="{{ url('pns/pelaksana/'.\Vinkla\Hashids\Facades\Hashids::encode($dataMaster->id_peg).'/download') }}">
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                                                               <span
                                                                                                   class="input-group-text"
                                                                                                   id="basic-addon1"><i
                                                                                                       class="fas fa-search"></i></span>
                                            </div>

                                            <input type="number" class="form-control" name="nip"
                                                   placeholder="Input NIP dan Tekan Enter"
                                                   aria-label="Username" aria-describedby="basic-addon1">

                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif


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
