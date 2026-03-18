@extends('shared.master')
@section('body_class', 'force-sticky-header')
@section('css')
    <style>
        #formApply .form-control {
            background: rgb(246, 246, 246) !important;
            border: 1px solid #f6f6f6 !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
        }
        
        #formApply .form-control:focus {
            background: rgb(246, 246, 246) !important;
            border-color: #ffffff !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
            outline: 0 !important;
        }

        #applyToastStack {
            position: fixed;
            top: 104px;
            right: 20px;
            z-index: 2000;
        }

        #applyToastStack .toast {
            min-width: 320px;
            margin-bottom: 10px;
            border: none;
            box-shadow: 0 10px 28px rgba(2, 45, 98, 0.2);
        }

        .feed-tooltip {
            position: relative;
            display: inline-block;
            opacity: unset;
        }

        .feed-tooltip-icon {
            color: #0b9444;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
        }

        .feed-tooltip .feed-tooltiptext {
            visibility: hidden;
            width: 220px;
            background-color: rgba(0, 0, 0, 0.85);
            color: #fff;
            text-align: left;
            border-radius: 4px;
            padding: 8px 10px;
            position: absolute;
            z-index: 10;
            bottom: 125%;
            left: 50%;
            margin-left: -110px;
            font-style: normal;
            line-height: 1.3;
        }

        .feed-tooltip:hover .feed-tooltiptext {
            visibility: visible;
        }
    </style>
@endsection

@section('content')
    <x-banner-summary mode="career"></x-banner-summary>
    <section class="header-farmingnew bg-white">
        <div class=" container">
            <div class="row align-items-center">
                <div class="col-xl-12">
                    <div class="blog-detail surface-contrast surface-strong surface-contrast-padded">
                        <div class="blog-post mb-4 ">
                            <div class="blog-post-content">
                                <div class="blog-post-info">
                                    <div>
                                        <a class="text-light" href="#">Posisi</a>
                                    </div>
                                </div>
                                <div class="blog-post-details">
                                    <h2 class="blog-post-title">
                                        {{ $rs->position }}
                                    </h2>
                                </div>
                                <div class="blog-post-info">
                                    <div>
                                        <a class="text-light" href="#">Lokasi</a>
                                        <h5 class="text-primary" href="#">{{ $rs->location }}</h5>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <form class="mt-4 row" id="formApply" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <div class="form-group col-12 input-group mb-5">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Dokumen</span>
                                        </div>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="formCV" name="formCV" accept=".pdf,application/pdf">
                                            <label class="custom-file-label" for="formCV">Upload CV / Portofolio-mu ( PDF )</label>
                                        </div>
                                        <span class="feed-tooltip ml-2 align-self-center">
                                            <i class="fas fa-question-circle feed-tooltip-icon" aria-hidden="true"></i>
                                            <span class="feed-tooltiptext">Maksimal 5 MB. PDF akan dioptimasi otomatis saat dikirim.</span>
                                        </span>
                                        <div id="cvInvalid" class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-md-12 mb-3">
                                        <input type="text" class="form-control" placeholder="Nama Depan"
                                            name="formFirstName" id="formFirstName" required >
                                        <input type="hidden" value="{{ encrypt($rs->id) }}" name="formCareerId">
                                    </div>
                                    <div class="form-group col-md-12 mb-3">
                                        <input type="text" class="form-control" placeholder="Nama Belakang"
                                            name="formLastName" id="formLastName" required>
                                    </div>
                                    <div class="form-group col-6 mb-3">
                                        <input type="email" class="form-control" placeholder="Email" name="formEmail"
                                            id="formEmail" required>
                                        <div id="emailInvalid" class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-6 mb-3">
                                        <input type="text" class="form-control" placeholder="Telp" name="formPhone"
                                            id="formPhone" required inputmode="numeric" maxlength="12" pattern="[0-9]{10,12}">
                                        <div id="phoneInvalid" class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-12 mb-3">
                                        <label>Tgl. Lahir</label>
                                        <input type="date" class="form-control" name="formBod" id="formBod" required>
                                    </div>
                                    <div class="form-group col-6 mb-3">
                                        <label for="formLastEducation">Pendidikan Terakhir</label>
                                        <select class="form-control" id="formLastEducation" name="formLastEducation"
                                            required>
                                            <option value="">Pilih Opsi</option>
                                            <option value="smk">SMK</option>
                                            <option value="diploma">Diploma</option>
                                            <option value="s1">S1</option>
                                            <option value="s2">S2</option>
                                            <option value="s3">S3</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-6 mb-3">
                                        <label for="formMajor">Jurusan</label>
                                        <input placeholder="Jurusan" class="form-control" name="formMajor" id="formMajor"
                                            required>
                                    </div>
                                    <div class="form-group col-12 mb-3">
                                        <label for="formIsExperience">Pengalaman Bekerja</label>
                                        <select class="form-control" id="formIsExperience" name="formIsExperience"
                                            required>
                                            <option value="">Pilih Opsi</option>
                                            <option value="0">Tidak Ada</option>
                                            <option value="1">Ada</option>
                                        </select>
                                    </div>
                                    <div id="have-experience" class="col-12" style="display:none">
                                        <table id="experienceTable" style="{ border-collapse:collapse }">
                                            <tbody>
                                            </tbody>
                                        </table>
                                        <div id="show-experience"></div>
                                        <div class="form-group col-12 mb-3">
                                            <button id="btnAddExperience" type="button"
                                                class="btn btn-primary btn-block">Tambah<i
                                                    class="fas fa-plus pl-3"></i></button>
                                        </div>
                                    </div>
                                    <div class="form-group col-6 mb-3">
                                       <label>Gaji Sekarang</label>
                                       <input placeholder="Rp. XXX" class="form-control" name="formCurrentSalary" id="formCurrentSalary" maxlength="8">
                                   </div>
                                    <div class="form-group col-6 mb-3">
                                        <label>Gaji yang diharapkan</label>
                                        <input placeholder="Rp. XXX" class="form-control" name="formExpectSalary" id="formExpectSalary" maxlength="8">
                                    </div>
                                    
                                    <div class="form-group col-12 mb-4">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="formAccept" required>
                                            <label class="custom-control-label small" for="formAccept">Setujui bahwa anda melamar di PT Sido Agung Group</label>
                                        </div>
                                        <div id="acceptedInvalid" class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-group col-12 mb-0">
                                        <button type="button" class="btn btn-primary btn-block" id="btnSubmit">Kirim<i
                                                class="fas fa-chevron-right pl-3"></i></button>
                                    </div>
                                </form>

                                <div id="exprience-list" style="display:none">
                                    <div class="row">
                                        <div class="form-group col-6 mb-3">
                                            <label>Nama Perusahaan</label>
                                            <input placeholder="Nama Perusahaan" class="form-control companyName" required>
                                        </div>
                                        <div class="form-group col-6 mb-3">
                                            <label>Industri</label>
                                            <input placeholder="Industri" class="form-control industri" required>
                                        </div>
                                        <div class="form-group col-6 mb-3">
                                            <label>Jabatan</label>
                                            <input placeholder="Jabatan" class="form-control position" required>
                                        </div>
                                        <div class="form-group col-4 mb-3">
                                            <label>Lama bekerja (tahun)</label>
                                            <input placeholder="Lama bekerja (tahun)" class="form-control lengthOfWork"
                                                required maxlength="2">
                                        </div>
                                        <div class="form-group col-2 mb-3">
                                            <label>Aksi</label>
                                            <button onclick="deleteRow(this)" type="button"
                                                class="btn btn-danger">Hapus</button>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="applyToastStack" aria-live="polite" aria-atomic="true"></div>
    
    <div class="modal fade" id="modalRespons" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-9 align-self-start align-self-lg-center ">
                            <p class="d-flex align-items-center mb-4">
                                <span class="font-weight-bold text-primary mr-2" id="teks_title">Thank you for your time, partner!</span>
                            </p>
                            <h5 class="mb-4 text-primary" id="teks_1">We will get back to you as soon as possible.
                            </h5>
                            <p class="mb-4 text-primary" id="teks_2">Let’s create many great stories!</p>
                            <p class="mb-4 text-primary" id="teks_3">Sincerely,</p>
                            <img class="img-fluid" src="{{ asset('images/saf/logo.png') }}" alt="">
                        </div>
                        <div class="col-sm-3 align-self-start align-self-lg-center ">
                            <img class="img-fluid " src="{{ asset('images/ai/telur.png') }}" alt=""
                                style="position: absolute; top:10px; right: 10px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var countTable = 0;
        var have_experience = $('#have-experience').html()

        $(function() {
            const allowExt = ["application/pdf"];
            const maxCvSize = 5 * 1024 * 1024; // 5MB
            const maxCvSizeLabel = "5 MB";

            @if (session()->has('success'))
               $("#modalRespons").modal("show");
               setTimeout(() => {
                  $("#modalRespons").modal("hide");
               }, 3000);
            @endif

            $("#have-experience").on("click", "#btnAddExperience", function(){
               var x = document.getElementById('experienceTable').insertRow(-1);
               var y = x.insertCell(0);

               let template = $('#exprience-list').html()
               let now_template = $(document).find('#show-experience').eq(0).html()
               now_template += template

               y.innerHTML = template
               $('#show-experience').show()
            })

            $('#formIsExperience').change(() => {
                var select_pengalaman = $('#formIsExperience').val()

                if (select_pengalaman == "1") {
                    $('#have-experience').html(have_experience)
                    $('#have-experience').show()
                } else {
                    $('#show-experience').html("")
                    $('#have-experience').hide()
                }
            })

            $("#formCurrentSalary, #formExpectSalary").keypress(function(e){
               let allow = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"]
               if(allow.includes(e.key))
                  return true
               return false
            })

            $("#formPhone").on("input", function(){
               this.value = (this.value || "").replace(/\D/g, "").slice(0, 12);
            });

            const syncCvLabel = function(input){
               const defaultLabel = "Upload CV / Portofolio-mu ( PDF )";
               const label = $(input).next(".custom-file-label");
               const file = input.files && input.files.length ? input.files[0] : null;

               label.text(file ? file.name : defaultLabel);
            };

            const showApplyToast = function(message, variant = "danger"){
               const isWarning = variant === "warning";
               const contextClass = isWarning ? "bg-warning text-dark" : "bg-danger text-white";
               const closeClass = isWarning ? "text-dark" : "text-white";
               const delay = isWarning ? 5200 : 4200;
               const toastId = "apply-toast-" + Date.now() + "-" + Math.floor(Math.random() * 1000);

               const toastHtml = `
                  <div id="${toastId}" class="toast ${contextClass}" role="alert" aria-live="assertive" aria-atomic="true" data-delay="${delay}">
                     <div class="toast-body d-flex justify-content-between align-items-center">
                        <span>${message}</span>
                        <button type="button" class="ml-2 mb-1 close ${closeClass}" data-dismiss="toast" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                  </div>
               `;

               const stack = $("#applyToastStack");
               stack.append(toastHtml);

               const toast = $("#" + toastId);
               toast.toast({ autohide: true, delay: delay });
               toast.toast("show");
               toast.on("hidden.bs.toast", function(){
                  $(this).remove();
               });
            };

            $("#formCV").on("change", function(){
               syncCvLabel(this);

               const files = this.files;
               if(!files || files.length < 1)
                  return;

               const file = files[0];
               const isPdfByMime = allowExt.includes((file.type || "").toLowerCase());
               const isPdfByExt = (file.name || "").toLowerCase().endsWith(".pdf");

               if (!(isPdfByMime || isPdfByExt)) {
                  $("#cvInvalid").text("Extensi file harus .pdf").css("display", "unset");
                  showApplyToast("Format file harus PDF (.pdf).", "danger");
                  this.value = "";
                  syncCvLabel(this);
                  return;
               }

               if(file.size > maxCvSize) {
                  $("#cvInvalid").text(`Maksimum size ${maxCvSizeLabel}`).css("display", "unset");
                  showApplyToast(`Ukuran CV terlalu besar. Batas maksimum ${maxCvSizeLabel}.`, "warning");
                  this.value = "";
                  syncCvLabel(this);
                  return;
               }

               $("#cvInvalid").text("").css("display", "none");
            });

            $(document).on("keypress", ".lengthOfWork", function(e){
               let allow = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"]
               if(allow.includes(e.key))
                  return true
               return false
            })

            $("#btnSubmit").on("click", function(){
               const formAccept = $("#formAccept");
               const emailInput = $("#formEmail");
               const phoneInput = $("#formPhone");
               const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
               const phonePattern = /^\d{10,12}$/;
               $("#cvInvalid").text("").css("display", "none");
               $("#acceptedInvalid").text("").css("display", "none");
               $("#emailInvalid").text("").css("display", "none");
               $("#phoneInvalid").text("").css("display", "none");
               let isValid = true;

               let check = [
                  "formFirstName", "formLastName", "formEmail", "formPhone",
                  "formBod", "formLastEducation", "formMajor", "formIsExperience",
                  "formCurrentSalary", "formExpectSalary"
               ];

               check.map(function(e){
                  let selector = $("#" + e);
                  selector.removeClass("is-invalid");
                  if(selector.val() == "")
                  {
                     isValid = false;
                     selector.addClass("is-invalid");
                  }
               });

               const emailValue = (emailInput.val() || "").trim();
               const phoneValue = (phoneInput.val() || "").trim();
               emailInput.val(emailValue);
               phoneInput.val(phoneValue);

               if(emailValue !== "" && !emailPattern.test(emailValue)) {
                  isValid = false;
                  emailInput.addClass("is-invalid");
                  $("#emailInvalid").text("Format email tidak valid.").css("display", "unset");
                  showApplyToast("Format email tidak valid.", "danger");
               }

               if(phoneValue !== "" && !phonePattern.test(phoneValue)) {
                  isValid = false;
                  phoneInput.addClass("is-invalid");
                  $("#phoneInvalid").text("Nomor telepon harus angka dengan panjang 10 sampai 12 digit.").css("display", "unset");
                  showApplyToast("Nomor telepon harus angka dengan panjang 10 sampai 12 digit.", "danger");
               }

               const experienceTable = $("#experienceTable")
               const experienceRows = experienceTable.find(".row")

               let countRow = 0;
               experienceRows.each(function(i, e){
                  const row = (i+1);
                  const that = $(this)
                  check = ["companyName", "industri", "position", "lengthOfWork"];

                  check.map(function(f){
                     let slc = that.find("." + f).eq(0)
                     slc.removeClass("is-invalid");
                     if(slc.val() == "")
                     {
                        isValid = false;
                        slc.addClass("is-invalid");
                     }
                     slc.prop("name", f + row);
                  })
                  countRow += row;
               })

               const cv = $("#formCV").prop('files');
               if(cv.length < 1)
               {
                  $("#cvInvalid").text("CV tidak boleh kosong").css("display", "unset");
                  return;
               }

               const file = cv[0];
               const isPdfByMime = allowExt.includes((file.type || "").toLowerCase());
               const isPdfByExt = (file.name || "").toLowerCase().endsWith(".pdf");
               if (!(isPdfByMime || isPdfByExt))
               {
                  $("#cvInvalid").text("Extensi file harus .pdf").css("display", "unset");
                  showApplyToast("Format file harus PDF (.pdf).", "danger");
                  return;
               }

               if(file.size > maxCvSize )
               {
                  $("#cvInvalid").text(`Maksimum size ${maxCvSizeLabel}`).css("display", "unset");
                  showApplyToast(`Ukuran CV terlalu besar. Batas maksimum ${maxCvSizeLabel}.`, "warning");
                  return;
               }


               if(!formAccept.is(":checked"))
               {
                  $("#acceptedInvalid").text("Peryataan belum disetujui.").css("display", "unset");
                  return;
               }

               if(isValid)
               {
                  $("#formApply")
                     .append(`<input type="hidden" value="${countRow}" name="totalRow" />`)
                     .prop("method", "post")
                     .prop("action", "{{route('we.job-apply')}}")
                     .submit();
               }
            })
        })

        function deleteRow(r) {
            var i = r.parentNode.parentNode.rowIndex;
            document.getElementById("experienceTable").deleteRow(i);
        }
    </script>
@endsection
