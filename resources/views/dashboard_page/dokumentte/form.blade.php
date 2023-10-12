@extends('mylayouts.app')
@section('title', 'Form '.ucwords($mode).' Dokumen TTE ')
@push('vendor-css')
    <link rel="stylesheet" href="{{ assetku('magnific-popup/magnific-popup.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/select2/dist/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets//modules/bootstrap-daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{assetku('freetransform/css/jquery.freetrans.css')}}"/>
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
            <h1>{{'Form '.ucwords($mode).' Dokumen TTE '}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{route('dokumen-tte')}}">Daftar Dokumen TTE </a></div>
                <div class="breadcrumb-item active">{{'Form '.ucwords($mode).' Dokumen TTE '}}</div>
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
            <form id="form" name="form" role="form" action="{{$action}}"
                  enctype="multipart/form-data" method="post" onsubmit="getbounds()">
                {{csrf_field()}}
                @if($mode=='ubah')
                    {{ method_field('PUT') }}
                @endif
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-primary">
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">No Dokumen</label>
                                        <div class="col-sm-4 col-lg-4">
                                            <div class="input-group">
                                 <span class="input-group-prepend">
                                 <label class="input-group-text">
                                 <i class="fa fa-list"></i></label>
                                 </span>
                                                <input class="form-control @error('no_dokumen') is-invalid @enderror"
                                                       required="required" name="no_dokumen" id="no_dokumen"
                                                       type="text" value="{{$no_dokumen}}" autofocus>
                                            </div>
                                            @error('no_dokumen')
                                            <div class="invalid-feedback">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Tgl Dokumen</label>
                                        <div class="col-sm-4 col-lg-4">
                                            <div class="input-group">
                                 <span class="input-group-prepend">
                                 <label class="input-group-text">
                                 <i class="fa fa-calendar"></i></label>
                                 </span>
                                                <input class="form-control @error('tgl_dokumen') is-invalid @enderror"
                                                       name="tgl_dokumen" id="tgl_dokumen"
                                                       type="text" value="{{$tgl_dokumen}}">
                                            </div>
                                            @error('tgl_dokumen')
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
                                            </div>
                                            @error('perihal')
                                            <div class="invalid-feedback">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Perangkat Daerah</label>
                                        <div class="col-sm-9 col-lg-9">
                                            @if(in_array(Auth::user()->level, ['admin', 'superadmin']))
                                                <select class="select_cari form-control" id="id_opd_fk"
                                                        name="id_opd_fk">
                                                    @foreach($listPerangkat as $nama => $value)
                                                        <option
                                                            value={{$value}} {{$value==$id_opd_fk ? 'selected' : ''}}>{{$nama}}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="hidden" name="id_opd_fk" id="id_opd_fk"
                                                       value="{{Auth::user()->id_opd_fk}}">
                                                <input type="text" readonly
                                                       class="form-control @error('id_opd_fk') is-invalid @enderror"
                                                       value="{{cek_opd(Auth::user()->id_opd_fk)->nama_opd}}">
                                            @endif
                                            @error('id_opd_fk')
                                            <div class="invalid-feedback">
                                                {{$message}}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Ditandatangani oleh</label>
                                        <div class="col-sm-9 col-lg-9">

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <select class="select_cari form-control" id="id_jenis_ttd_fk"
                                                            name="id_jenis_ttd_fk"
                                                            @if($status_dokumen=='final') disabled @endif>
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


                                            @error('id_jenis_ttd_fk')
                                            <div class="invalid-feedback">
                                                {{$message}}
                                            </div>
                                            @enderror

                                        </div>
                                    </div>
                                    <div class="row mb-3" id="divBerkas"
                                         @if($status_dokumen=='final') style="display: none" @endif>
                                        <label class="col-sm-3 col-lg-3 col-form-label">@if($berkas)
                                                Ubah
                                            @else
                                                Unggah
                                            @endif Berkas</label>
                                        <div class="col-sm-4 col-lg-4">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($status_dokumen !='final')
                        <div class="col-lg-12" id="ttd-coor">

                            <div class="card card-warning">
                                <div class="card-body text-center">
                                    <h4>Atur Posisi Tanda Tangan Anda</h4>
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

                        </div>
                @endif
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
            if ($("#tgl_dokumen").length) {
                $('#tgl_dokumen').daterangepicker({
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

            //tampilPesan('info', 'Silahkan lanjutkan transaksi anda! sx: ' + x + ' y: ' + y, 'Berhasil mengambil koordinat');

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

        $(function () {
            listTTD();
            //getImagettd();
            $('#ttd-coor').removeClass('d-none')
            initTransform()
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
            // getImagettd();
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
    </script>
@endpush
