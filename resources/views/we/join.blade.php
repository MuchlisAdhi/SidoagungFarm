@extends('shared.master')

@section('content')
    <x-banner-summary mode="contact"></x-banner-summary>

    <section class="space-ptb background-sidoagung">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12 pb-lg-0">
                    <div class="section-title mb-3 pt-3">
                        <h2 class="text-white">Bergabung Menjadi Mitra</h2>
                    </div>
                    <p class="text-white">Isi data di bawah ini untuk mendaftar sebagai mitra PT. Sidoagung Farm.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="space-ptb">
        <div class="container">
            <div class="row justify-content-lg-around position-relative pt-5">
                <div class="col-lg-12 col-md-12 pr-lg-5">
                    <div class="p-4 p-md-5 bg-white border-radius surface-contrast surface-medium">
                        <h4>Form Pendaftaran Mitra</h4>
                        <form class="mt-4" id="frmJoinPartner" method="post" action="{{ route('we.join-as-partner') }}">
                            {{ csrf_field() }}
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-3">
                                    <input type="text" class="form-control" id="formFirstName" name="formFirstName"
                                        placeholder="Nama Depan" required>
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <input type="text" class="form-control" id="formLastName" name="formLastName"
                                        placeholder="Nama Belakang" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-3">
                                    <input type="date" class="form-control" id="formBod" name="formBod" required>
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <input type="email" class="form-control" id="formEmail" name="formEmail"
                                        placeholder="Alamat Surel" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 mb-3">
                                    <input type="text" class="form-control" id="formPhone" name="formPhone"
                                        placeholder="Nomor Telepon" required>
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <input type="text" class="form-control" id="formCategory" name="formCategory"
                                        value="Kemitraan" readonly required>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" class="form-control" id="formCompanyName" name="formCompanyName"
                                    placeholder="Nama Perusahaan" required>
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" class="form-control" id="formCompanyLocation"
                                    name="formCompanyLocation" placeholder="Lokasi Perusahaan" required>
                            </div>
                            <div class="form-group mb-4">
                                <textarea class="form-control" id="formCompanyDescription" name="formCompanyDescription"
                                    placeholder="Deskripsi Perusahaan" rows="5" maxlength="10000" required></textarea>
                            </div>
                            <div class="form-group mb-0 text-right">
                                <button type="button" id="btnSubmit" class="btn btn-danger text-white">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('we.modal-response')
@endsection

@section('script')
    <script>
        $(function() {
            @if (session()->has('success'))
                $("#modalRespons").modal("show");
                setTimeout(() => {
                    $("#modalRespons").modal("hide");
                }, 7000);
            @endif

            $("#btnSubmit").click(function() {
                let valid = true;
                const check = [
                    "formFirstName",
                    "formLastName",
                    "formBod",
                    "formEmail",
                    "formPhone",
                    "formCategory",
                    "formCompanyName",
                    "formCompanyLocation",
                    "formCompanyDescription"
                ];

                check.map(function(e) {
                    const x = $("#" + e);
                    x.removeClass("is-invalid");
                    if (x.val() == "") {
                        x.addClass("is-invalid");
                        valid = false;
                    }
                });

                if (!isEmail($("#formEmail").val())) {
                    $("#formEmail").addClass("is-invalid");
                    valid = false;
                }

                if (valid) {
                    $("#frmJoinPartner").submit();
                }
            });
        });

        function isEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }
    </script>
@endsection
