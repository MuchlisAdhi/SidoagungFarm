@extends('admin.master')
@section('page')
    Pelamar => {{$career->position}}
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-lg-12" style="margin-bottom: 5px;">
                    <div class="pull-right">
                        <a href="{{ route('admin.feedback.karir.export-applicants', ['careerId' => encrypt($career->id)]) }}" class="btn btn-success btn-sm">
                            <i class="fa fa-file-excel-o"></i> Export to Excel
                        </a>
                    </div>
                </div>
            </div>
            <table id="tblCareer" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 15%;">Name</th>
                        <th style="width: 15%;">Email</th>
                        <th style="width: 15%;">Phone</th>
                        <th>Education</th>
                        <th style="width: 5%;">Status</th>
                        <th style="width: 10%;">Show</th>
                    </tr>
                </thead>
                <tbody id="tblBodyCareer">
                    @foreach ($list as $l)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $l->firstname }} {{$l->lastname}}</td>
                            <td>{{ $l->email }}</td>
                            <td>{{ $l->phone }}</td>
                            <td>{{ $l->lasteducation }} {{$l->major}}</td>
                            <td>
                                @if($l->isapprove)
                                    <span class="text-success text-bold">Approve</span>
                                @else
                                    @if($l->rejectreason == "")
                                        <span class="text-warning text-bold">New</span>
                                    @else
                                        <span class="text-danger text-bold">Reject</span>
                                    @endif
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0);" class="text-success" title="Edit"
                                    onclick="show('{{ encrypt($l->id) }}')">
                                    <i class="fa fa-eye" style="font-size: 20px;"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="modalDetail">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group form-group-sm">
                                        <label for="formFullName" class="col-sm-3 control-label">Full Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formFullName">
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label for="formEmail" class="col-sm-3 control-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formEmail">
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label for="formPhone" class="col-sm-3 control-label">Phone</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formPhone">
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label for="formBod" class="col-sm-3 control-label">BOD</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formBod">
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label for="formEducation" class="col-sm-3 control-label">Last Education</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formEducation">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group form-group-sm">
                                        <label for="formEducation" class="col-sm-3 control-label">Experienced</label>
                                        <div class="col-sm-9">
                                            <span id="experienced" class="text-bold"></span>
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label for="formCurrentSalary" class="col-sm-3 control-label">Current Salary</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formCurrentSalary">
                                        </div>
                                    </div>
                                    <div class="form-group form-group-sm">
                                        <label for="formExpectationSalary" class="col-sm-3 control-label">Expectation Salary</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formExpectationSalary">
                                        </div>
                                    </div>

                                    <hr />

                                    <div class="form-group form-group-sm">
                                        <label for="formStatus" class="col-sm-3 control-label">Status</label>
                                        <div class="col-sm-9">
                                            <span id="formStatus" class="text-bold"></span>
                                        </div>
                                    </div>

                                    <div class="form-group form-group-sm" id="reasonBlock">
                                        <label for="formRejectReason" class="col-sm-3 control-label">Reject Reason</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control input-sm" id="formRejectReason"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-lg-12">
                                    <hr />
                                    <h5 class="text-bold text-success">Experiences</h5>
                                    <table id="tblExpList" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">No.</th>
                                                <th>Company Name</th>
                                                <th>Industri</th>
                                                <th>Position</th>
                                                <th style="width: 10px">Duration</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tblExpBody"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <hr />
                                    <h5 class="text-bold text-success">Curriculum Vitae</h5>
                                    <div class="pdf-toolbar">
                                        <button type="button" class="btn btn-default btn-sm" id="btnPdfPrev" disabled>
                                            <i class="fa fa-chevron-left"></i> Prev
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm" id="btnPdfNext" disabled>
                                            Next <i class="fa fa-chevron-right"></i>
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm" id="btnPdfZoomOut" disabled>
                                            <i class="fa fa-search-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm" id="btnPdfZoomIn" disabled>
                                            <i class="fa fa-search-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm" id="btnPdfPrint" disabled>
                                            <i class="fa fa-print"></i> Print
                                        </button>
                                        <button type="button" class="btn btn-default btn-sm" id="btnPdfDownload" disabled>
                                            <i class="fa fa-download"></i> Download
                                        </button>
                                        <span id="pdfPageInfo" class="label label-default">Halaman 0 / 0</span>
                                        <span id="pdfZoomInfo" class="label label-info">Zoom 100%</span>
                                    </div>
                                    <div id="cvPdfViewport">
                                        <canvas id="cvPdfCanvas"></canvas>
                                        <div id="pdfViewerEmpty">CV belum tersedia.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="btnReject">Reject</button>
                    <button type="button" class="btn btn-primary" id="btnApprove">Approve</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modalGiveReason">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Reject Reason</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group form-group-sm">
                            <div class="col-sm-12">
                                <textarea class="form-control input-sm" id="formGiveReason"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnSubmitReason">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css') }}">
    <style>
        .pdf-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        #cvPdfViewport {
            border: 1px solid #d2d6de;
            border-radius: 4px;
            background: #f5f7fa;
            overflow: auto;
            max-height: 65vh;
            min-height: 320px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 12px;
        }

        #cvPdfCanvas {
            display: none;
            max-width: 100%;
            height: auto;
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.14);
        }

        #pdfViewerEmpty {
            color: #707070;
            font-weight: 600;
            width: 100%;
            text-align: center;
            border: 1px dashed #bfc5cc;
            border-radius: 4px;
            padding: 28px 14px;
            background: #fcfcfc;
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js" referrerpolicy="no-referrer"></script>
    <script>
        var selected;
        var pdfDoc = null;
        var pageNum = 1;
        var pageRendering = false;
        var pageNumPending = null;
        var pdfScale = 1;
        var pdfCanvas = null;
        var pdfContext = null;
        var currentCvUrl = null;
        var currentCvFileName = "cv.pdf";
        const PDF_DEFAULT_SCALE = 1;
        const PDF_MIN_SCALE = 0.7;
        const PDF_MAX_SCALE = 2.5;

        function getCvResourceUrl(cvid) {
            return `{{ url('/getResource') }}/${encodeURIComponent(cvid)}`;
        }

        function setPdfControlState(isEnabled) {
            $("#btnPdfPrev, #btnPdfNext, #btnPdfZoomOut, #btnPdfZoomIn, #btnPdfPrint, #btnPdfDownload").prop("disabled", !isEnabled);
        }

        function sanitizeFileName(text) {
            return (text || "cv")
                .toString()
                .replace(/\s+/g, "_")
                .replace(/[^a-zA-Z0-9_\-.]/g, "")
                .replace(/_+/g, "_")
                .replace(/^_+|_+$/g, "")
                || "cv";
        }

        function showViewerToastError(message) {
            if (typeof $ !== "undefined" && typeof $.toast === "function") {
                $.toast({
                    heading: "Error",
                    text: message,
                    showHideTransition: "fade",
                    position: "bottom-right",
                    icon: "error"
                });
                return;
            }

            alert(message);
        }

        function updatePdfMeta() {
            const totalPage = pdfDoc ? pdfDoc.numPages : 0;
            $("#pdfPageInfo").text(`Halaman ${totalPage ? pageNum : 0} / ${totalPage}`);
            $("#pdfZoomInfo").text(`Zoom ${Math.round(pdfScale * 100)}%`);
        }

        function resetPdfViewer(message = "CV belum tersedia.") {
            pdfDoc = null;
            pageNum = 1;
            pageRendering = false;
            pageNumPending = null;
            pdfScale = PDF_DEFAULT_SCALE;
            currentCvUrl = null;
            currentCvFileName = "cv.pdf";
            setPdfControlState(false);

            if (pdfCanvas && pdfContext) {
                pdfContext.clearRect(0, 0, pdfCanvas.width, pdfCanvas.height);
            }

            $("#cvPdfCanvas").hide();
            $("#pdfViewerEmpty").text(message).show();
            updatePdfMeta();
        }

        function renderPdfPage(num) {
            if (!pdfDoc) {
                return;
            }

            pageRendering = true;
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({ scale: pdfScale });
                pdfCanvas.height = viewport.height;
                pdfCanvas.width = viewport.width;

                const renderContext = {
                    canvasContext: pdfContext,
                    viewport: viewport
                };

                return page.render(renderContext).promise;
            }).then(function() {
                pageRendering = false;

                if (pageNumPending !== null) {
                    const pending = pageNumPending;
                    pageNumPending = null;
                    renderPdfPage(pending);
                }

                updatePdfMeta();
            }).catch(function() {
                resetPdfViewer("Gagal menampilkan halaman PDF.");
            });
        }

        function queueRenderPdfPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPdfPage(num);
            }
        }

        function loadCvPdf(cvid, fileName = "cv.pdf") {
            if (!cvid) {
                resetPdfViewer("CV belum tersedia.");
                return;
            }

            if (typeof window.pdfjsLib === "undefined") {
                resetPdfViewer("PDF.js gagal dimuat.");
                return;
            }

            setPdfControlState(false);
            $("#pdfViewerEmpty").text("Memuat CV PDF...").show();
            $("#cvPdfCanvas").hide();

            const resourceUrl = getCvResourceUrl(cvid);
            currentCvUrl = resourceUrl;
            currentCvFileName = sanitizeFileName(fileName).replace(/\.pdf$/i, "") + ".pdf";
            const loadingTask = window.pdfjsLib.getDocument({ url: resourceUrl });
            loadingTask.promise.then(function(loadedPdf) {
                pdfDoc = loadedPdf;
                pageNum = 1;
                pdfScale = PDF_DEFAULT_SCALE;
                setPdfControlState(true);

                $("#pdfViewerEmpty").hide();
                $("#cvPdfCanvas").show();
                updatePdfMeta();
                renderPdfPage(pageNum);
            }).catch(function() {
                resetPdfViewer("Gagal memuat CV PDF.");
            });
        }

        $(function() {
            if (typeof window.pdfjsLib !== "undefined") {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
            }

            pdfCanvas = document.getElementById("cvPdfCanvas");
            pdfContext = pdfCanvas ? pdfCanvas.getContext("2d") : null;
            resetPdfViewer("Pilih pelamar untuk melihat CV.");

            $("#tblCareer").DataTable();

            $("#btnApprove").click(function(){
                if (!selected) {
                    return;
                }

                if (!AdminSubmit.start("#btnApprove", "Menyimpan...")) {
                    return;
                }

                $.get("{{ url('/admin/feedback/karir/approveApp') }}/" + selected, function(r) {
                    window.location.reload()
                }).fail(function() {
                    AdminSubmit.stop("#btnApprove");
                    $.toast({
                        heading: 'Error',
                        text: "Gagal, Hubungi Administrator.",
                        showHideTransition: 'fade',
                        position: 'bottom-right',
                        icon: 'error'
                    })
                });
            });

            $("#btnReject").click(function(){
                $("#formGiveReason").val("")
                $("#formGiveReason").removeClass("has-error");
                $("#modalDetail").modal("hide")
                $("#modalGiveReason").modal("show")
            })

            $("#btnSubmitReason").click(function(){
                let reason = $("#formGiveReason")
                if(reason.val() == "")
                {
                    reason.parents(".form-group").addClass("has-error");
                    return;
                }

                if (!AdminSubmit.start("#btnSubmitReason", "Menyimpan...")) {
                    return;
                }

                let form = new FormData();
                form.append('id', selected);
                form.append('reason', reason.val());

                $.ajax({
                    url: "{{ url('/admin/feedback/karir/rejectApp') }}",
                    type: "POST",
                    data: form,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(res) {
                        if (res.code == 200) {
                            window.location.reload();
                        } else {
                            AdminSubmit.stop("#btnSubmitReason");
                            $.toast({
                                heading: 'Error',
                                text: res.msg,
                                showHideTransition: 'fade',
                                position: 'bottom-right',
                                icon: 'error'
                            })
                        }
                    },
                    error: function(e) {
                        AdminSubmit.stop("#btnSubmitReason");
                        $.toast({
                            heading: 'Error',
                            text: "Gagal, Hubungi Administrator.",
                            showHideTransition: 'fade',
                            position: 'bottom-right',
                            icon: 'error'
                        })
                    }
                });
            });

            $("#btnPdfPrev").click(function() {
                if (!pdfDoc || pageNum <= 1) {
                    return;
                }
                pageNum--;
                queueRenderPdfPage(pageNum);
            });

            $("#btnPdfNext").click(function() {
                if (!pdfDoc || pageNum >= pdfDoc.numPages) {
                    return;
                }
                pageNum++;
                queueRenderPdfPage(pageNum);
            });

            $("#btnPdfZoomOut").click(function() {
                if (!pdfDoc) {
                    return;
                }
                const nextScale = Math.max(PDF_MIN_SCALE, pdfScale - 0.2);
                if (nextScale === pdfScale) {
                    return;
                }
                pdfScale = nextScale;
                queueRenderPdfPage(pageNum);
            });

            $("#btnPdfZoomIn").click(function() {
                if (!pdfDoc) {
                    return;
                }
                const nextScale = Math.min(PDF_MAX_SCALE, pdfScale + 0.2);
                if (nextScale === pdfScale) {
                    return;
                }
                pdfScale = nextScale;
                queueRenderPdfPage(pageNum);
            });

            $("#btnPdfDownload").click(function() {
                if (!currentCvUrl) {
                    return;
                }

                const link = document.createElement("a");
                link.href = currentCvUrl;
                link.download = currentCvFileName;
                link.rel = "noopener noreferrer";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });

            $("#btnPdfPrint").click(function() {
                if (!currentCvUrl) {
                    return;
                }

                const printWindow = window.open(currentCvUrl, "_blank");
                if (!printWindow) {
                    showViewerToastError("Popup print diblokir browser. Izinkan popup untuk situs ini.");
                    return;
                }

                printWindow.focus();
                const triggerPrint = function() {
                    try {
                        printWindow.print();
                    } catch (e) {
                        // Browser PDF plugin handles print dialog.
                    }
                };

                printWindow.addEventListener("load", function() {
                    setTimeout(triggerPrint, 600);
                }, { once: true });
            });

            $("#modalDetail").on("hidden.bs.modal", function() {
                resetPdfViewer("Pilih pelamar untuk melihat CV.");
            });
        })

        function show(id) {
            selected = id;
            $.get("{{ url('/admin/feedback/karir/getApplicant') }}/" + id, function(r) {
                if(r.code == 200)
                {
                    const {firstname, lastname, email, phone, bod, lasteducation, major, currentsalary, expectationsalary, isexperience, experiencelist, isapprove, rejectreason, cvid  } = r.data

                    $("#formFullName").val(`${firstname} ${lastname}`)
                    $("#formEmail").val(email)
                    $("#formPhone").val(phone)
                    $("#formBod").val(bod)
                    $("#formEducation").val(`${lasteducation} ${major}`)
                    
                    $("#experienced").text(isexperience == "1" ? "Yes" : "No")
                    $("#formCurrentSalary").val(formatNumber(currentsalary))
                    $("#formExpectationSalary").val(formatNumber(expectationsalary))

                    if(isapprove == "1")
                    {
                        $("#formStatus").text("Approve");
                        $("#reasonBlock").hide()
                    }else{
                        if(rejectreason != null && rejectreason != "")
                        {
                            $("#formStatus").text("Reject");
                            $("#formRejectReason").val(rejectreason)
                            $("#reasonBlock").show()
                        }else{
                            $("#formStatus").text("New");
                            $("#reasonBlock").hide()
                        }
                    }

                    const expList = (() => {
                        try {
                            return JSON.parse(experiencelist || "[]");
                        } catch (e) {
                            return [];
                        }
                    })();
                    let html = ``;
                    expList.map(function(e, i){
                        html += `<tr><td>${i+1}</td><td>${e.companyName}</td><td>${e.industri}</td><td>${e.position}</td><td>${e.lengthOfWork}</td></tr>`
                    })
                    $("#tblExpBody").html(html)
                    loadCvPdf(cvid, `${firstname || "cv"}_${lastname || ""}`);

                    $("#modalDetail").modal("show")
                }
            });
        }

        function formatNumber(num) {
            if (num === null || typeof num === "undefined" || num === "") {
                return "";
            }

            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }
    </script>
@endsection
