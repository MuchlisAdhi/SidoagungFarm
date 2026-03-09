@extends('shared.master')

@section('content')
    <x-banner-summary mode="career"></x-banner-summary>
    <section class="header-farmingnew bg-white">
        <div class=" container">
            <div class="row align-items-center">
                <div class="col-xl-12">
                    <div class="blog-detail">
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
                                            <input type="file" class="custom-file-input" id="formCV" name="formCV">
                                            <label class="custom-file-label" for="formCV">Upload CV / Portofolio-mu ( PDF )</label>
                                        </div>
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
                                    </div>
                                    <div class="form-group col-6 mb-3">
                                        <input type="text" class="form-control" placeholder="Telp" name="formPhone"
                                            id="formPhone" required>
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
                            <img class="img-fluid" src="{{ asset('images/logo.png') }}" alt="">
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

            $(document).on("keypress", ".lengthOfWork", function(e){
               let allow = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"]
               if(allow.includes(e.key))
                  return true
               return false
            })

            $("#btnSubmit").on("click", function(){
               const formAccept = $("#formAccept");
               const allowExt = ["application/pdf"];
               $("#cvInvalid").text("").css("display", "none");
               $("#acceptedInvalid").text("").css("display", "none");
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
               if (!allowExt.includes(file.type))
               {
                  $("#cvInvalid").text("Extensi file harus .pdf").css("display", "unset");
                  return;
               }

               if(file.size > 250000 )
               {
                  $("#cvInvalid").text("Maksimum size 250kb").css("display", "unset");
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
