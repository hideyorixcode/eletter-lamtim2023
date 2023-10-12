@extends('mylayouts.app')
@section('title', 'Form '.ucwords($mode).' Surat Langsung ')
@push('vendor-css')
<link rel="stylesheet" href="{{ assetku('magnific-popup/magnific-popup.min.css')}}">
<link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
<link rel="stylesheet" href="{{assetku('assets//modules/bootstrap-daterangepicker/daterangepicker.css')}}">
<style>
   .select_sm {
   height: 33.22222px !important;
   padding-bottom: 2px !important;
   padding-top: 2px !important;
   padding-right: 2px !important;
   padding-left: 2px !important;
   }
</style>
@endpush
@section('content')
<section class="section">
   <div class="section-header">
      <h1>{{'Form '.ucwords($mode).' Surat Langsung '}}</h1>
      <div class="section-header-breadcrumb">
         <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>
         <div class="breadcrumb-item"><a href="{{route('surat-langsung')}}">Daftar Surat Langsung</a></div>
         <div class="breadcrumb-item active">{{'Form '.ucwords($mode).' Surat Langsung '}}</div>
      </div>
   </div>
   <div class="section-body">
      <div class="alert alert-primary">
         Kode, Indek, dan Upload Berkas dilakukan oleh Bagian Umum.
      </div>
      <form id="form" name="form" role="form" action="{{$action}}" enctype="multipart/form-data" method="post">
         <div class="row">
            <div class="col-sm-6">
               <div class="card">
                  {{csrf_field()}}
                  @if($mode=='ubah')
                  {{ method_field('PUT') }}
                  @endif
                  <div class="card-body">
                     <div class="col-sm-12">
                        @if($mode=='ubah')
                        <div class="row mb-3">
                           <label class="col-sm-3 col-lg-3 col-form-label">Kode</label>
                           <div class="col-sm-9 col-lg-9">
                              <div class="input-group">
                                 <span class="input-group-prepend">
                                 <label class="input-group-text">
                                 <i class="fa fa-flag"></i></label>
                                 </span>
                                 <input class="form-control @error('kode') is-invalid @enderror"
                                    name="kode" id="kode"
                                    type="text" readonly value="{{$kode}}">
                                  @error('kode')
                                                                <div class="invalid-feedback">
                                                                   {{$message}}
                                                                </div>
                                                                @enderror
                              </div>

                           </div>
                        </div>
                        <div class="row mb-3">
                           <label class="col-sm-3 col-lg-3 col-form-label">Indek</label>
                           <div class="col-sm-9 col-lg-9">
                              <div class="input-group">
                                 <span
                                    class="input-group-prepend">
                                 <label
                                    class="input-group-text">
                                 <i class="fa fa-bell"></i></label>
                                 </span>
                                 <input class="form-control @error('indek') is-invalid @enderror"
                                    name="indek" id="indek"
                                    type="text" readonly value="{{$indek}}">
                                  @error('indek')
                                                              <div class="invalid-feedback">
                                                                 {{$message}}
                                                              </div>
                                                              @enderror
                              </div>

                           </div>
                        </div>
                        @endif
                        <div class="row mb-3">
                           <label class="col-sm-3 col-lg-3 col-form-label">No Surat</label>
                           <div class="col-sm-9 col-lg-9">
                              <div class="input-group">
                                 <span class="input-group-prepend">
                                 <label class="input-group-text">
                                 <i class="fa fa-list"></i></label>
                                 </span>
                                 <input class="form-control @error('no_surat') is-invalid @enderror"
                                    required="required" name="no_surat" id="no_surat"
                                    type="text" value="{{$no_surat}}" autofocus>
                                  @error('no_surat')
                                                                <div class="invalid-feedback">
                                                                   {{$message}}
                                                                </div>
                                                                @enderror
                              </div>

                           </div>
                        </div>
                        <div class="row mb-3">
                           <label class="col-sm-3 col-lg-3 col-form-label">Tgl Surat</label>
                           <div class="col-sm-9 col-lg-9">
                              <div class="input-group">
                                 <span class="input-group-prepend">
                                 <label class="input-group-text">
                                 <i class="fa fa-calendar"></i></label>
                                 </span>
                                 <input class="form-control @error('tgl_surat') is-invalid @enderror"
                                    name="tgl_surat" id="tgl_surat"
                                    type="text" value="{{$tgl_surat}}">
                                  @error('tgl_surat')
                                                               <div class="invalid-feedback">
                                                                  {{$message}}
                                                               </div>
                                                               @enderror
                              </div>

                           </div>
                        </div>
                        <div class="row mb-3">
                           <label class="col-sm-3 col-lg-3 col-form-label">Lampiran</label>
                           <div class="col-sm-9 col-lg-9">
                              <div class="input-group">
                                 <span class="input-group-prepend">
                                 <label class="input-group-text">
                                 <i class="fa fa-sort-numeric-up"></i></label>
                                 </span>
                                 <input class="form-control @error('lampiran') is-invalid @enderror"
                                    name="lampiran" id="lampiran"
                                    type="number" value="{{$lampiran}}">
                                  @error('lampiran')
                                                                <div class="invalid-feedback">
                                                                   {{$message}}
                                                                </div>
                                                                @enderror
                              </div>

                           </div>
                        </div>
                        <div class="row mb-3">
                           <label class="col-sm-3 col-lg-3 col-form-label">Dari</label>
                           <div class="col-sm-9 col-lg-9">
                              <div class="input-group">
                                 <span
                                    class="input-group-prepend">
                                 <label
                                    class="input-group-text">
                                 <i class="fa fa-user"></i></label>
                                 </span>
                                 <input class="form-control @error('dari') is-invalid @enderror"
                                    name="dari" id="dari"
                                    type="text" value="{{$dari}}">
                                  @error('dari')
                                                               <div class="invalid-feedback">
                                                                  {{$message}}
                                                               </div>
                                                               @enderror
                              </div>

                           </div>
                        </div>
                        <div class="row mb-3">
                           <label class="col-sm-3 col-lg-3 col-form-label">Kepada</label>
                           <div class="col-sm-9 col-lg-9">
                              <select class="select_cari form-control" id="kepada"
                                 name="kepada">
                              @foreach($listPerangkat as $nama => $value)
                              <option
                              value={{$value}} {{$value==$kepada ? 'selected' : ''}}>{{$nama}}</option>
                              @endforeach
                              </select>
                              @error('kepada')
                              <div class="invalid-feedback">
                                 {{$message}}
                              </div>
                              @enderror
                           </div>
                        </div>
                        <div class="row mb-3">
                           <label class="col-sm-3 col-lg-3 col-form-label">Hal</label>
                           <div class="col-sm-9 col-lg-9">
                              <div class="input-group">
                                 <span
                                    class="input-group-prepend">
                                 <label
                                    class="input-group-text">
                                 <i class="fa fa-align-right"></i></label>
                                 </span>
                                 <input class="form-control @error('perihal') is-invalid @enderror"
                                    name="perihal" id="perihal"
                                    type="text" value="{{$perihal}}">
                                  @error('perihal')
                                                               <div class="invalid-feedback">
                                                                  {{$message}}
                                                               </div>
                                                               @enderror
                              </div>

                           </div>
                        </div>
                        @if($mode=='ubah')

                        <div class="row mb-3">
                           <label class="col-sm-3 col-lg-3 col-form-label">Berkas</label>

                               @if($berkas)
                                <div class="col-sm-9 col-lg-9">
                              <a href="{{url('berkas/'.$berkas)}}" target="_blank">Lihat Berkas
                              saat
                              ini</a>
                                    @error('berkas')
                                                                 <p style="color:red">
                                                                    {{$message}}
                                                                 </p>
                                                                 @enderror
                                </div>
                               @else
                                   <label class="col-sm-9 col-lg-9 col-form-label text-warning">Belum Diupload Bagian Umum</label>
                               @endif


                        </div>

                        @endif
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="card">
                  <div class="card-body">
                     <div class="row mb-3">
                        <label class="col-sm-3 col-lg-3 col-form-label">Tgl Masuk</label>
                        <div class="col-sm-9 col-lg-9">
                           <div class="input-group">
                              <span
                                 class="input-group-prepend">
                              <label
                                 class="input-group-text">
                              <i class="fa fa-calendar"></i></label>
                              </span>
                              <input
                                 class="form-control datetimepickerindo @error('tgl_masuk') is-invalid @enderror"
                                 name="tgl_masuk" id="tgl_masuk" type="text" value="{{$tgl_masuk}}"/>
                               @error('tgl_masuk')
                                                          <div class="invalid-feedback">
                                                             {{$message}}
                                                          </div>
                                                          @enderror
                           </div>

                        </div>
                     </div>
                     <div class="row mb-3">
                        <label class="col-sm-3 col-lg-3 col-form-label">Nama Penerima</label>
                        <div class="col-sm-9 col-lg-9">
                           <div class="input-group">
                              <span
                                 class="input-group-prepend">
                              <label
                                 class="input-group-text">
                              <i class="fa fa-user"></i></label>
                              </span>
                              <input class="form-control @error('nama_penerima') is-invalid @enderror"
                                 name="nama_penerima" id="nama_penerima"
                                 type="text" value="{{$nama_penerima}}">
                               @error('nama_penerima')
                                                         <div class="invalid-feedback">
                                                            {{$message}}
                                                         </div>
                                                         @enderror
                           </div>

                        </div>
                     </div>
                     <div class="row mb-3">
                        <label class="col-sm-3 col-lg-3 col-form-label">Catatan Perjalanan Surat</label>
                        <div class="col-sm-9 col-lg-9">
                           <textarea class="form-control" name="catatan" id="catatan" style="min-height: 100px">{{$catatan}}</textarea>
                           @error('catatan')
                           <div class="invalid-feedback">
                              {{$message}}
                           </div>
                           @enderror
                        </div>
                     </div>
                     <div class="row mb-3">
                        <label class="col-sm-3 col-lg-3 col-form-label">Tujuan Disposisi</label>
                        <div class="col-sm-9 col-lg-9">
                           <select class="select_cari form-control" id="tujuan"
                              name="tujuan[]" multiple {{$editTujuan==1 ? '' : 'disabled'}}>
                           @foreach($listPimpinan as $nama => $value)
                           <option
                           value={{$value}} {{array_search($value, explode (",", $tujuan)) !== false ? 'selected' : ''}}>{{$nama}}</option>
                           @endforeach
                           </select>
                           @error('tujuan')
                           <div class="invalid-feedback">
                              {{$message}}
                           </div>
                           @enderror
                        </div>
                     </div>
                     <div class="row mb-3">
                        <label class="col-sm-3 col-lg-3 col-form-label">Status</label>
                        <div class="col-sm-9 col-lg-9">
                           <input type="text" readonly value="DITERUSKAN" class="form-control">
                           @error('status')
                           <div class="invalid-feedback">
                              {{$message}}
                           </div>
                           @enderror
                        </div>
                     </div>
                     <div class="row mb-3">
                        <label class="col-sm-3 col-lg-3 col-form-label">Melalui</label>
                        <div class="col-sm-9 col-lg-9">
                           <input type="text" readonly value="BAGIAN UMUM" class="form-control">
                           @error('melalui')
                           <div class="invalid-feedback">
                              {{$message}}
                           </div>
                           @enderror
                        </div>
                     </div>
                  </div>
                  <div class="card-footer text-right bg-whitesmoke">
                     @if($mode=='tambah')
                     <button type="reset" class="btn btn-secondary mr-2">Reset Form</button>
                     @endif
                     <button type="submit" class="btn btn-primary mr-2"><i class="mr-50 fa fa-save"></i>
                     @if($mode=='ubah') Simpan Perubahan @else Submit @endif
                     </button>
                  </div>
               </div>
            </div>
         </div>
      </form>
   </div>
</section>
@endsection
@push('scripts')
<script src="{{assetku('assets/modules/select2/dist/js/select2.full.min.js')}}"></script>
<script src="{{assetku('assets/modules/jquery-ui/jquery-ui.min.js')}}"></script>
<script src="{{ assetku('magnific-popup/jquery.magnific-popup.min.js')}}"></script>
<script src="{{ assetku('assets/modules/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
<script src="{{ assetku('assets/jshideyorix/general.js')}}"></script>
<!--begin::Page Scripts(used by this page)-->
<script type="text/javascript">
   @if(session('pesan_status'))
   tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
   @endif

   if (jQuery().daterangepicker) {
       if ($("#tgl_surat").length) {
           $('#tgl_surat').daterangepicker({
               locale: {format: 'DD/MM/YYYY'},
               singleDatePicker: true,
           });
       }
       // if ($("#tgl_masuk").length) {
       //     $('#tgl_masuk').daterangepicker({
       //         locale: {format: 'DD/MM/YYYY'},
       //         singleDatePicker: true,
       //     });
       // }

       if ($(".datetimepickerindo").length) {
           $('.datetimepickerindo').daterangepicker({
               locale: {format: 'DD/MM/YYYY HH:mm'},
               singleDatePicker: true,
               timePicker: true,
               timePicker24Hour: true,
           });
       }

   }
</script>
@endpush
