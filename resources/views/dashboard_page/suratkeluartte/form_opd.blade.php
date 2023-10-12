@extends('mylayouts.app')
@section('title', 'Form '.ucwords($mode).' Surat Keluar TTE')
@push('vendor-css')
    <link rel="stylesheet" href="{{ assetku('magnific-popup/magnific-popup.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets//modules/bootstrap-daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{assetku('freetransform/css/jquery.freetrans.css')}}"/>
    <link rel="stylesheet" href="{{assetku('assets/modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css')}}">
    <style>
        .select_sm {
            height: 33.22222px !important;
            padding-bottom: 2px !important;
            padding-top: 2px !important;
            padding-right: 2px !important;
            padding-left: 2px !important;
        }

        #the-canvas {
            border: 1px solid black;
        }
    </style>
@endpush
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{'Form '.ucwords($mode).' Surat Keluar TTE'}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{route('surat-keluar')}}">Daftar Surat Keluar </a></div>
                <div class="breadcrumb-item active">{{'Form '.ucwords($mode).' Surat Keluar '}}</div>
            </div>
        </div>
        <div class="section-body">
            @component('components.infoubahversipdf')
            @endcomponent
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
            <form id="form" name="form" role="form" action="{{$action}}" onsubmit="getbounds()"
                  enctype="multipart/form-data" method="post">
                {{csrf_field()}}
                @if($mode=='ubah')
                    {{ method_field('PUT') }}
                @endif
                <input name="diisi_oleh" id="diisi_oleh" type="hidden" value="{{$diisi_oleh}}">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group" id="div_nosurat">
                                            <label>No Surat</label>
                                            <div class="input-group">
                           <span
                               class="input-group-prepend">
                           <label
                               class="input-group-text">
                           <i class="fa fa-list"></i></label>
                           </span>
                                                <input class="form-control @error('no_surat') is-invalid @enderror"
                                                       name="no_surat" id="no_surat"
                                                       type="text" value="{{$no_surat}}">
                                            </div>
                                            @error('no_surat')
                                            <div class="invalid-feedback">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Tgl Surat</label>

                                            <div class="input-group">
                              <span class="input-group-prepend">
                              <label class="input-group-text">
                              <i class="fa fa-calendar"></i></label>
                              </span>
                                                <input class="form-control @error('tgl_surat') is-invalid @enderror"
                                                       name="tgl_surat" id="tgl_surat"
                                                       type="text" value="{{$tgl_surat}}">
                                            </div>
                                            @error('tgl_surat')
                                            <div class="invalid-feedback">
                                                {{$message}}
                                            </div>
                                            @enderror

                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>Lampiran</label>


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

                                <div class="form-group">
                                    <label>Dari</label>

                                    <input type="text" readonly
                                           class="form-control @error('id_opd_fk') is-invalid @enderror"
                                           value="{{cek_opd($id_opd_fk)->nama_opd}}">
                                    <input type="hidden" readonly class="form-control" name="id_opd_fk"
                                           id="id_opd_fk" value="{{$id_opd_fk}}">
                                    @error('id_opd_fk')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror

                                </div>
                                <div class="form-group">
                                    <label>Tujuan</label>
                                    <br/>
                                    <div class="form-check form-check-inline mb-3">
                                        <input class="form-check-input" type="radio" id="inlineradio1"
                                               name="tujuan" value="dalam"
                                               onchange="cektujuan()" @if($tujuan=='dalam') checked @endif>
                                        <label class="form-check-label" for="inlineradio1">Antar Instansi</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="inlineradio2"
                                               name="tujuan" value="luar"
                                               onchange="cektujuan()"
                                               @if($tujuan=='luar' || $tujuan=='') checked @endif>
                                        <label class="form-check-label" for="inlineradio2">Lainnya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="inlineradio3"
                                               name="tujuan" value="keduanya"
                                               onchange="cektujuan()" @if($tujuan=='keduanya') checked @endif>
                                        <label class="form-check-label" for="inlineradio3">Atur Keduanya</label>
                                    </div>
                                    <div id="divkepadaselect">
                                        <select class="select_cari_placeholder form-control" id="kepada_id_opd"
                                                name="kepada_id_opd[]" multiple>
                                            @foreach($listPerangkat as $nama => $value)
                                                <option
                                                    value="{{$value.';'.$nama}}" {{array_search($value, explode (",", $kepada_id_opd)) !== false ? 'selected' : ''}}>{{$nama}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <input class="form-control @error('kepada') is-invalid @enderror"
                                           name="kepada" id="kepada"
                                           type="text" value="{{$kepada}}"
                                           placeholder="Ketik Tujuan Lainnya jika lebih dari 1 gunakan tanda titik koma (;)">
                                    @error('kepada')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror

                                </div>

                                <div class="form-group">
                                    <label>Hal</label>

                                    <div class="input-group">
                              <span
                                  class="input-group-prepend">
                              <label
                                  class="input-group-text">
                              <i class="fa fa-align-right"></i></label>
                              </span>
                                        <input class="form-control @error('perihal') is-invalid @enderror"
                                               name="perihal" id="perihal"
                                               type="text" value="{{$perihal}}" autofocus>
                                    </div>
                                    @error('perihal')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Bagikan ke Protokol Pimpinan (untuk diproses ke pimpinan daerah)</label>
                                    <select class="form-control" id="bagikan_tu" name="bagikan_tu">
                                        <option value="ya" {{$bagikan_tu=='ya' ? 'selected' : ''}}>YA</option>
                                        <option value="tidak" {{$bagikan_tu=='tidak' ? 'selected' : ''}}>TIDAK
                                        </option>
                                    </select>
                                    <ul>
                                        <li>
                                            pilih <strong>YA</strong>, jika surat akan ditandatangani oleh Pejabat
                                            (Sekretaris
                                            Daerah/Asisten 1/Asisten 2/Asisten 3/Bupati/Wakil Bupati)
                                        </li>
                                        <li>
                                            pilih <strong>TIDAK</strong>, jika surat akan ditandatangani oleh Kepala
                                            Perangkat Daerah dan No Surat diisi oleh admin Perangkat Daerah
                                        </li>
                                    </ul>
                                    @error('bagikan_tu')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Ditandatangani oleh</label>
                                    <div id="div_jenisttd">

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <select class="select_cari form-control" id="id_jenis_ttd_fk"
                                                        name="id_jenis_ttd_fk"
                                                        @if($status_sk=='final') disabled @endif>
                                                    @foreach($listJenis as $jenis)
                                                        <option
                                                            value={{$jenis->id_jenis_ttd}} {{$jenis->id_jenis_ttd==$id_jenis_ttd_fk ? 'selected' : ''}}>{{$jenis->jenis_ttd.' - '.$jenis->nama_opd}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-6">
                                                <select class="select_cari form-control" id="visualisasi_tte"
                                                        name="visualisasi_tte">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control" id="jenisttdread" name="jenisttdread"
                                           placeholder readonly value="Diinput oleh Protokol Pimpinan">
                                    @error('id_jenis_ttd_fk')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group" id="divBerkas"
                                     @if($status_sk=='final') style="display: none" @endif>
                                    <label>@if($berkas)
                                            Ubah
                                        @else
                                            Unggah
                                        @endif Berkas</label>
                                    <input name="berkas" id="berkas" type="file"
                                           class="form-control"
                                           accept="application/pdf">
                                    @if($berkas)
                                        <br/>
                                        <a href="{{url('berkas/temp/'.$berkas)}}" target="_blank">Lihat Berkas
                                            saat
                                            ini</a>
                                        <br/>
                                        <input type="checkbox" name="remove_berkas"
                                               value="{{$berkas}}">
                                        Hapus
                                        Berkas
                                        Ketika Disimpan
                                    @endif
                                    @error('berkas')
                                    <p style="color:red">
                                        {{$message}}
                                    </p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Akses Download</label>
                                    <select class="form-control" name="is_download" id="is_download">
                                        <option value=1 {{$is_download==1 ? 'selected' : ''}}>YA</option>
                                        <option value=0 {{$is_download==0 ? 'selected' : ''}}>TIDAK</option>
                                    </select>
                                    @error('is_download')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="card-body text-center @if($status_sk !='final') d-none @endif" id="ttd-coor">
                            <h4 id="judulmetode">Atur Posisi Tanda Tangan Anda</h4>
                            <input type="hidden" id="y" name="y">
                            <input type="hidden" id="x" name="x">
                            <input type="hidden" id="halaman" name="halaman">
                            <input type="hidden" id="width-ttd" name="width">
                            <input type="hidden" id="height-ttd" name="height">
                            <img
                                id="two"
                                src="@if(isset($dataMaster->ttd)) {{ url('uploads/' . $dataMaster->ttd->img_ttd) }} @else {{ assetku('assets/img/example-image.jpg') }} @endif"
                                class="trans"
                                width="100"
                                height="100"
                            />
                            <div>
                                <button type="button" class="btn-success btn" id="prev">Halaman Sebelumnya
                                </button>
                                <button type="button" class="btn btn-success" id="next">Halaman Selanjutnya
                                </button>
                                &nbsp; &nbsp;
                                <span
                                >Halaman: <span id="page_num"></span> / <span id="page_count"></span
                                    ></span>
                            </div>
                            <canvas style="margin-top: 10px;" id="the-canvas"></canvas>
                        </div>
                    </div>
                    <div class="card-footer text-right bg-whitesmoke">
                        @if($mode=='tambah')
                            <button type="reset" class="btn btn-secondary mr-2">Reset Form</button>
                        @endif
                        <button type="submit" class="btn btn-primary mr-2"><i class="mr-50 fa fa-save"></i>
                            @if($mode=='ubah')
                                Simpan Perubahan
                            @else
                                Submit
                            @endif
                        </button>
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
    <script src="{{ assetku('freetransform/js/Matrix.js') }}"></script>
    <script src="{{ assetku('freetransform/js/jquery.freetrans.js') }}"></script>
    <script src="{{ assetku('pdf-js/pdf.js') }}"></script>
    <script src="{{ assetku('assets/modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js')}}"></script>
    <!--begin::Page Scripts(used by this page)-->
    <script type="text/javascript">
        @if(session('pesan_status'))
        tampilPesan('{{session('pesan_status.tipe')}}', '{{session('pesan_status.desc')}}', '{{session('pesan_status.judul')}}');
        @endif
        var pdfBlob = null;

        var pdfjsLib = window["pdfjs-dist/build/pdf"];
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            "{{ assetku('pdf-js/pdf.worker.js') }}";
        var pdfDoc = null,
            pageNum = 1,
            pageRendering = false,
            pageNumPending = null,
            scale = 1,
            canvas = document.getElementById("the-canvas"),
            ctx = canvas.getContext("2d");

        if (jQuery().daterangepicker) {
            if ($("#tgl_surat").length) {
                $('#tgl_surat').daterangepicker({
                    locale: {format: 'DD/MM/YYYY'},
                    singleDatePicker: true,
                });
            }
        }

        document.getElementById("berkas").addEventListener("change", async function ({target}) {
            if (target.files && target.files.length) {
                pdfBlob = await convertFileToBase64(target.files[0]);
                showPDF(pdfBlob.split(';base64,')[1])
            }
        })

        function convertFileToBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = reject;
            });
        }

        function getbounds() {
            var b = $("#two").freetrans("getBounds");

            var pdf = getPositionXY(document.getElementById("the-canvas"));
            var widthTTD = b.xmin - pdf.left;
            var x = b.xmax - pdf.left;
            var y = pdf.bottom - b.ymin;
            var heightTTD = pdf.bottom - b.ymax;
            var halaman = pageNum;

            $('#y').val(y)
            $('#x').val(x)

            $('#width-ttd').val(widthTTD)
            $('#height-ttd').val(heightTTD)
            $('#halaman').val(halaman)

            tampilPesan('info', 'Silahkan lanjutkan transaksi anda! sx: ' + x + ' y: ' + y, 'Berhasil mengambil koordinat');

        }

        /**
         * Get page info from document, resize canvas accordingly, and render page.
         * @param num Page number.
         */
        function renderPage(num) {
            pageRendering = true;
            // Using promise to fetch the page
            pdfDoc.getPage(num).then(function (page) {
                var viewport = page.getViewport({scale: scale});

                canvas.height = viewport.height;
                canvas.width = viewport.width

                // Render PDF page into canvas context
                var renderContext = {
                    canvasContext: ctx,
                    viewport: viewport,
                };
                var renderTask = page.render(renderContext);

                // Wait for rendering to finish
                renderTask.promise.then(function () {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        // New page rendering is pending
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });

            // Update page counters
            document.getElementById("page_num").textContent = num;
        }

        function getPositionXY(element) {
            let box = element.getBoundingClientRect();
            return {
                top: box.top + window.pageYOffset,
                right: box.right + window.pageXOffset,
                bottom: box.bottom + window.pageYOffset,
                left: box.left + window.pageXOffset
            };
        }

        /**
         * If another page rendering in progress, waits until the rendering is
         * finised. Otherwise, executes rendering immediately.
         */
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        /**
         * Displays previous page.
         */
        function onPrevPage() {
            if (pageNum <= 1) {
                return;
            }
            pageNum--;
            queueRenderPage(pageNum);
        }

        document.getElementById("prev").addEventListener("click", onPrevPage);

        /**
         * Displays next page.
         */
        function onNextPage() {
            if (pageNum >= pdfDoc.numPages) {
                return;
            }
            pageNum++;
            queueRenderPage(pageNum);
        }

        document.getElementById("next").addEventListener("click", onNextPage);

        /**
         * Asynchronously downloads PDF.
         */
        function showPDF(data) {
            var pdfData = atob(data)
            pdfjsLib.getDocument({data: pdfData}).promise.then(function (pdfDoc_) {
                pdfDoc = pdfDoc_;
                document.getElementById("page_count").textContent = pdfDoc.numPages;

                // Initial/first page rendering
                renderPage(pageNum);
            });
        }

        function initTransform() {
            $("#two").freetrans({
                x: 100,
                y: 100,
            });
        }

        if (jQuery().select2) {
            $(".select_cari_placeholder").select2(
                {
                    placeholder: "Pilih Perangkat Daerah (bisa lebih dari 1 perangkat daerah)",
                    allowClear: true
                }
            );
        }


        $(function () {
            listTTD();
            //getImagettd();
            $('#ttd-coor').removeClass('d-none')
            cekpembagian()
            cektujuan();
        });

        function getImagettd() {
            var value = $('#visualisasi_tte').val();
            $.ajax({
                url: '{{ url('dashboard/get-ttd-image') }}',
                type: 'GET',
                data: {
                    id: value
                },
                success(res) {
                    $('#two').attr('src', res)
                    initTransform()
                },
                error(res) {

                }
            });
        }

        $('#id_jenis_ttd_fk').change(function () {
            listVisualisasi();
        });

        $('#visualisasi_tte').change(function () {
            // getImagettd();
            getImagettd();
        });

        function listTTD() {
            //var kategori = $('#kategori_ttd').val();
            var kategori = 'elektronik';
            $.ajax({
                url: '{{ url('dashboard/get-list-ttd') }}',
                type: 'GET',
                data: {
                    kategori: kategori,
                    id_jenis_ttd_fk: '{{$id_jenis_ttd_fk}}',
                },
                success: function (res) {
                    $('select[name="id_jenis_ttd_fk"]').html(res);
                },
                error(res) {
                    console.log(res);
                }
            });
        }

        function listVisualisasi() {
            $.ajax({
                url: '{{ url('dashboard/get-list-visualisasi') }}',
                type: 'GET',
                data: {
                    id_jenis_ttd_fk: $('#id_jenis_ttd_fk').val(),
                },
                success: function (res) {
                    $('select[name="visualisasi_tte"]').html(res);
                    // getImagettd();
                },
                error(res) {
                    console.log(res);
                }
            });
        }

        $('#bagikan_tu').change(function () {
            cekpembagian();
        })

        function cekpembagian() {
            if ($('#bagikan_tu').val() === 'ya') {
                //$('#div_nosurat').addClass('d-none')
                $('#no_surat').val('');
                $('#no_surat').attr('readonly', true);
                $('#no_surat').attr('placeholder', 'Diinput oleh Protokol Pimpinan');
                $('#div_tandatangan').addClass('d-none')
                $('#div_jenisttd').addClass('d-none')
                $('#ttd-coor').addClass('d-none')
                $('#jenisttdread').removeClass('d-none');
            } else {
                //$('#div_nosurat').removeClass('d-none')
                $('#jenisttdread').addClass('d-none');
                $('#no_surat').attr('readonly', false);
                $('#no_surat').attr('placeholder', 'Diinput oleh Perangkat Daerah');
                $('#div_tandatangan').removeClass('d-none')
                $('#div_jenisttd').removeClass('d-none')


                $('#ttd-coor').removeClass('d-none')

                //$('#ttd-coor').removeClass('d-none')
            }
        }

        function cektujuan() {
            var cektujuan = $('input[name="tujuan"]:checked').val();
            // alert(cektujuan);
            if (cektujuan == 'luar') {
                $('#kepada').show();
                $('#divkepadaselect').addClass('d-none');
            } else if (cektujuan == 'dalam') {
                $('#kepada').hide();
                $('#divkepadaselect').removeClass('d-none');
            } else {
                //alert('hei');
                //$('#kepada').text('');
                $('#kepada').show();
                $('#divkepadaselect').removeClass('d-none');
            }
        }

        //cekpembagian();
    </script>
@endpush
