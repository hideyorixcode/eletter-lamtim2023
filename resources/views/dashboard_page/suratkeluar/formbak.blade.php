@extends('mylayouts.app')
@section('title', 'Form '.ucwords($mode).' Surat Keluar ')
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
            <h1>{{'Form '.ucwords($mode).' Surat Keluar '}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{route('surat-keluar')}}">Daftar Surat Keluar </a></div>
                <div class="breadcrumb-item active">{{'Form '.ucwords($mode).' Surat Keluar '}}</div>
            </div>
        </div>
        <div class="section-body">
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
                  enctype="multipart/form-data" method="post">
                {{csrf_field()}}
                @if($mode=='ubah')
                    {{ method_field('PUT') }}
                @endif


                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-7">
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
                                        </div>
                                        @error('no_surat')
                                        <div class="invalid-feedback">
                                            {{$message}}
                                        </div>
                                        @enderror
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
                                        </div>
                                        @error('tgl_surat')
                                        <div class="invalid-feedback">
                                            {{$message}}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-lg-3 col-form-label">Dari</label>
                                    <div class="col-sm-9 col-lg-9">
                                        <select class="select_cari form-control" id="id_opd_fk"
                                                name="id_opd_fk">
                                            @foreach($listPerangkat as $nama => $value)
                                                <option
                                                    value={{$value}} {{$value==$id_opd_fk ? 'selected' : ''}}>{{$nama}}</option>
                                            @endforeach
                                        </select>
                                        @error('id_opd_fk')
                                        <div class="invalid-feedback">
                                            {{$message}}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-lg-3 col-form-label">Kepada</label>
                                    <div class="col-sm-9 col-lg-9">
                                        <div class="input-group">
                                 <span
                                     class="input-group-prepend">
                                 <label
                                     class="input-group-text">
                                 <i class="fa fa-user"></i></label>
                                 </span>
                                            <input class="form-control @error('kepada') is-invalid @enderror"
                                                   name="kepada" id="kepada"
                                                   type="text" value="{{$kepada}}">
                                        </div>
                                        @error('kepada')
                                        <div class="invalid-feedback">
                                            {{$message}}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-lg-3 col-form-label">Lampiran</label>
                                    <div class="col-sm-9 col-lg-9">
                                        <div class="input-group">
                                 <span
                                     class="input-group-prepend">
                                 <label
                                     class="input-group-text">
                                 <i class="fa fa-clipboard-list"></i></label>
                                 </span>
                                            <input class="form-control @error('lampiran') is-invalid @enderror"
                                                   name="lampiran" id="lampiran"
                                                   type="number" value="{{$lampiran}}">
                                        </div>
                                        @error('lampiran')
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
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label>Metode Tanda Tangan</label>
                                    <select class="form-control" id="kategori_ttd"
                                            name="kategori_ttd"
                                            @if($kategori_ttd=='elektronik' && $status_sk=='final') disabled @endif>
                                        <option value="basah" {{$kategori_ttd=='basah' ? 'selected' : ''}}>Tanda
                                            Tangan
                                            Basah
                                        </option>
                                        <option
                                            value="elektronik" {{$kategori_ttd=='elektronik' ? 'selected' : ''}}>
                                            Tanda Tangan Elektronik
                                        </option>
                                    </select>
                                    @error('kategori_ttd')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Ditandatangani oleh</label>
                                    <select class="select_cari form-control" id="id_jenis_ttd_fk"
                                            name="id_jenis_ttd_fk"
                                            @if($kategori_ttd=='elektronik' && $status_sk=='final') disabled @endif>
                                        @foreach($listJenis as $jenis)
                                            <option
                                                value={{$jenis->id_jenis_ttd}} {{$jenis->id_jenis_ttd==$id_jenis_ttd_fk ? 'selected' : ''}}>{{$jenis->jenis_ttd.' - '.$jenis->nama_opd}}</option>
                                        @endforeach
                                    </select>
                                    @error('id_jenis_ttd_fk')
                                    <div class="invalid-feedback">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group" id="divBerkas"
                                     @if($kategori_ttd=='elektronik' && $status_sk=='final') style="display: none" @endif>
                                    <label>@if($berkas) Ubah @else
                                            Unggah @endif Berkas</label>
                                    <input name="berkas" id="berkas" type="file"
                                           class="form-control"
                                           accept="application/pdf">
                                    @if($berkas)
                                        <br/>
                                        @if($kategori_ttd=='basah')
                                            <a href="{{url('berkas/'.$berkas)}}" target="_blank">Lihat Berkas saat
                                                ini</a>
                                        @else
                                            <a href="{{url('berkas/temp/'.$berkas)}}" target="_blank">Lihat Berkas
                                                saat
                                                ini</a>
                                        @endif
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

                    @if($status_sk !='final')
                        <div class="col-sm-12">
                            <div class="card-body text-center" id="ttd-coor">
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
                                    <button type="button" class="btn btn-primary" type="button"
                                            onclick="getbounds()">Ambil
                                        Koordinat
                                    </button>
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
                    @endif
                    <div class="card-footer text-right bg-whitesmoke">
                        @if($mode=='tambah')
                            <button type="reset" class="btn btn-secondary mr-2">Reset Form</button>
                        @endif
                        <button type="submit" class="btn btn-primary mr-2"><i class="mr-50 fa fa-save"></i>
                            @if($mode=='ubah') Simpan Perubahan @else Submit @endif
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

        $(function () {
            if ($('#kategori_ttd').val() === 'elektronik') {
                $('#ttd-coor').removeClass('d-none')
            } else {
                $('#ttd-coor').addClass('d-none')

            }
            $('#kategori_ttd').change(function () {
                listTTD();
                if ($(this).val() === 'elektronik') {
                    $('#ttd-coor').removeClass('d-none')
                } else {
                    $('#ttd-coor').addClass('d-none')
                }
            })
            $('#id_jenis_ttd_fk').change(function () {
                if ($('#kategori_ttd').val() === 'elektronik') {
                    var value = $(this).val();
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
            })
            initTransform()
        });

        function listTTD() {
            var kategori = $('#kategori_ttd').val();
            $.ajax({
                url: '{{ url('dashboard/get-list-ttd') }}',
                type: 'GET',
                data: {
                    kategori: kategori,
                    id_jenis_ttd_fk : '{{$id_jenis_ttd_fk}}',
                },
                success: function (res) {
                    $('select[name="id_jenis_ttd_fk"]').html(res);
                },
                error(res) {
                    console.log(res);
                }
            });
        }
    </script>
@endpush
