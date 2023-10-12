@extends('mylayouts.app')
@section('title', 'Form Verifikasi Dokumen TTE ')
@push('vendor-css')

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
            <h1>{{'Form Verifikasi Dokumen TTE '}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></div>

                <div class="breadcrumb-item active">{{'Form Verifikasi Dokumen TTE '}}</div>
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
            <form class="form" id="form" name="form" method="post" enctype="multipart/form-data"
                  action="javascript:save();">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-primary">
                            <div class="card-body">
                                <div class="col-sm-12">


                                    <div class="row mb-3" id="divBerkas">
                                        <label class="col-sm-3 col-lg-3 col-form-label">Unggah Berkas</label>
                                        <div class="col-sm-4 col-lg-4">
                                            <input name="signed_file" id="signed_file" type="file"
                                                   class="form-control" onchange="form.submit()"
                                                   accept="application/pdf">

                                            @error('signed_file')
                                            <p style="color:red" id="error_signed_file">
                                                {{$message}}
                                            </p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
{{--                            <div class="card-footer text-right bg-whitesmoke">--}}

{{--                                <button type="submit" class="btn btn-primary mr-2"><i class="mr-50 fa fa-upload"></i>--}}
{{--                                    Submit--}}
{{--                                </button>--}}
{{--                            </div>--}}
                        </div>
                    </div>

                    <div class="col-lg-6" id="divhasil">
                        <div class="card card-success">
                            <div class="card-header">
                                <h4 id="nama_dokumen">Data Verifikasi</h4>
                            </div>
                            <div class="card-body">
                                @include('components.loader')
                                <div id="divsuccess" style="display: none">
                                    <div class="alert alert-success alert-has-icon">
                                        <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                                        <div class="alert-body">
                                            <div class="alert-title">DOCUMENT VALID !!!</div>
                                            Tanda tangan elektronik valid dan dokumen merupakan dokumen asli sejak
                                            ditandatangani (Sertifikat BSrE)
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-striped">
                                        <tbody>
                                        <tr>
                                            <td class="font-weight-bold">Penandatangan</td>
                                            <td id="signer_name"></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Diterbitkan Oleh</td>
                                            <td id="issuer_dn"></td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Waktu Tandatangan</td>
                                            <td id="signed_in"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="divgagal" style="display: none">
                                    <div class="alert alert-danger alert-has-icon">
                                        <div class="alert-icon"><i class="fas fa-times"></i></div>
                                        <div class="alert-body">
                                            <div class="alert-title">DOCUMENT INVALID !!!</div>
                                            Tidak ditemukan tanda tangan elektronik valid yang dikeluarkan oleh BSrE.
                                        </div>
                                    </div>
                                </div>
                                <div id="divnotif">
                                    <div class="alert alert-info alert-has-icon">
                                        <div class="alert-icon"><i class="fas fa-info"></i></div>
                                        <div class="alert-body">
                                            <div class="alert-title">PILIH BERKAS</div>
                                            unggah berkas yang telah ditandatangani secara elektronik menggunakan
                                            sertifikat BSrE lalu klik tombol submit
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-6" id="ttd-coor">

                        <div class="card card-warning">
                            <div class="card-body text-center">


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

                </div>

            </form>
        </div>
    </section>
@endsection
@push('scripts')

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


        document.getElementById("signed_file").addEventListener("change", async function ({target}) {
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


        function save() {
            $(".loaderData").show();
            var url;
            var _method;
            var id;
            var formData = new FormData($('#form')[0]);


            url = "{{ url('dashboard/verifikasi-tte/submit/') }}";
            _method = "POST";


            var token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': token
                },
                url: url,
                type: 'POST',
                data: formData,
                dataType: "JSON",
                cache: false,
                contentType: false,
                processData: false,
                success: function (data, textStatus, jqXHR) {
                    //console.log(textStatus + ": " + jqXHR.status);
                    // do something with data
                    $(".loaderData").hide();

                    //console.log(jqXHR.status);
                    //console.log(data);
                    //console.log(data.details[0].info_signer.signer_name);
                    if (data.summary != null) {
                        $("#divsuccess").show();
                        $("#divgagal").hide();
                        $("#divnotif").hide();
                        $("#signer_name").text(data.details[0].info_signer.signer_name);
                        $("#issuer_dn").text(data.details[0].info_signer.issuer_dn);
                        $("#signed_in").text(data.details[0].signature_document.signed_in);

                    } else {
                        $("#divsuccess").hide();
                        $("#divgagal").show();
                        $("#divnotif").hide();
                        $("#signer_name").text('');
                        $("#issuer_dn").text('');
                        $("#signed_in").text('');

                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus + ": " + jqXHR.status + " " + errorThrown);
                }


            });
        }


    </script>
@endpush
