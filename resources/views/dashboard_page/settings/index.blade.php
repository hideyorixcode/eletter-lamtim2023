@extends('mylayouts.app')
@section('title', 'Settings Aplikasi')
@push('vendor-css')
@endpush
@section('content')

    <section class="section">
        <div class="section-header">
            <h1>Konfigurasi Aplikasi</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item">Konfigurasi Aplikasi</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Daftar Konfigurasi</h2>
            <p class="section-lead">
                Ubah Konfigurasi Aplikasi dan Simpan Perubahan nya
            </p>

            <div class="row mt-sm-4">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card card-primary">

                        <!--begin::Card-->
                        <form id="form" method="post"
                              enctype="multipart/form-data"
                              action="{{url('dashboard/update-settings')}}">
                            <!--begin::Form-->

                            {{csrf_field()}}
                            {{ method_field('PUT') }}

                            <div class="card-body">
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
                                <div class="row">
                                    <div class="col-lg-6">
                                        <?php
                                        $countTextfield = 0;
                                        foreach ($settingsText as $x) :
                                        ?>
                                        <input type="hidden" id="setting_Id" name="setting_Id[]"
                                               value="{{$x->setting_Id }}">
                                        <input type="hidden" id="setting_Type" name="setting_Type[]"
                                               value="{{ $x->setting_Type}}">
                                        <?php if ($x->setting_Type == 'email') {
                                            $editor = 'email';
                                        } else if ($x->setting_Type == 'number') {
                                            $editor = 'number';
                                        } else {
                                            $editor = 'text';
                                        }
                                        ?>
                                        <div class="form-group row">
                                            <label
                                                class="col-xl-3 col-lg-3 col-form-label">{{$x->setting_Label}}</label>
                                            <div class="col-lg-9 col-xl-9">
                                                <input
                                                    class="form-control @error('setting_Value') is-invalid @enderror"
                                                    name="setting_Value[]" id="{{$x->setting_Key}}" type="{{$editor}}"
                                                    value="{{$x->setting_Value}}" @if($countTextfield==0) {{'autofocus'}} @endif/>
                                                @error('setting_Value')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <?php
                                        $countTextfield++;
                                        endforeach;
                                        ?>
                                    </div>
                                    <!--end::Wizard Step 1-->
                                    <div class="col-lg-6">
                                        <?php
                                        $countTextarea = 0;
                                        foreach ($settingsTextarea as $i) :
                                        ?>
                                        <div class="form-group row">
                                            <label
                                                class="col-xl-3 col-lg-3 col-form-label">{{$i->setting_Label}}</label>
                                            <div class="col-lg-9 col-xl-9">
                                                <input type="hidden" id="setting_Id" name="setting_Id[]"
                                                       value="{{$i->setting_Id }}">
                                                <input type="hidden" id="setting_Type" name="setting_Type[]"
                                                       value="{{ $i->setting_Type}}">
                                                <textarea id="{{$i->setting_Key}}" name="setting_Value[]" rows="3"
                                                          class="form-control @error('setting_Value') is-invalid @enderror">{{$i->setting_Value}}</textarea>
                                                @error('setting_Value')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <?php
                                        $countTextarea++;
                                        endforeach;
                                        ?>

                                        <hr/>

                                        <?php
                                        $countGambar = 0;
                                        foreach ($settingsGambar as $z) :
                                        ?>
                                        <div class="form-group row">
                                            <label
                                                class="col-xl-3 col-lg-3 col-form-label">{{$z->setting_Label}}</label>
                                            <input type="hidden" id="idberkas" name="idberkas[]"
                                                   value="<?= $z->setting_Id ?>">
                                            <input type="hidden" id="setting_Typeberkas"
                                                   name="setting_Typeberkas[]"
                                                   value="<?= $z->setting_Type ?>">
                                            <input type="hidden" id="setting_Valueberkas"
                                                   name="setting_Valueberkas[]"
                                                   value="<?= $z->setting_Value ?>">
                                            <?php if ($z->setting_Type == 'gambar') {
                                                $filepilihan = 'image/*';
                                            } else if ($z->setting_Type == 'favicon') {
                                                $filepilihan = '.ico';
                                            } else {
                                                $filepilihan = '.pdf';
                                            }
                                            ?>
                                            <div class="row">
                                                <div class="col-xl-3 col-lg-3">
                                                    <img
                                                        src="{{ $z->setting_Value ? url('uploads/' . $z->setting_Value) :url('uploads/blank.png') }}"
                                                        class="img-thumbnail img-preview_{{ $countGambar }}">
                                                </div>
                                                <div class="col-xl-6 col-lg-6">
                                                    <div class="custom-file mb-3">
                                                        <input type="file" class="custom-file-input"
                                                               id="berkas_{{ $countGambar }}"
                                                               name="berkas_{{ $countGambar }}"
                                                               onchange="previewImg({{ $countGambar }})"
                                                               accept="<?= $filepilihan ?>">
                                                        <label class="custom-file-label"
                                                               id="custom-file-label_{{ $countGambar }}"
                                                               for="validatedCustomFile">Pilih
                                                            {{ $z->setting_Label }}...</label>
                                                        @error($z->setting_Key)
                                                        <p style="color: red">
                                                            {{ $message }}
                                                        </p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        $countGambar++;
                                        endforeach;
                                        ?>
                                    </div>


                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Simpan
                                    Perubahan
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{assetku('assets/jshideyorix/general.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            @if(session('pesan_status'))
            tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
            @endif
        });

        function previewImg(id) {
            const logo = document.querySelector('#berkas_' + id);
            const logoLabel = document.querySelector('#custom-file-label_' + id);
            const logoPreview = document.querySelector('.img-preview_' + id);

            logoLabel.textContent = logo.files[0].name;

            const fileLogo = new FileReader();
            fileLogo.readAsDataURL(logo.files[0]);

            fileLogo.onload = function (e) {
                logoPreview.src = e.target.result;
            }
        }
    </script>
@endpush
