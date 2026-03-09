@extends('shared.master')

@section('content')
    <x-banner-summary mode="contact"></x-banner-summary>
    <section class="space-ptb background-sidoagung">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12 pb-lg-0">
                    <div class="section-title mb-3 pt-3">
                        <h2 class="text-white">Hubungi Kami</h2>
                    </div>
                    <p class="text-white">Sebagai Bagian Dari Layanan Konsumen PT. Sido Agung Agro Prima, Kami
                        Membuka Kanal - Kanal Komunikasi Yang Dapat Dengan Mudah Diakses.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="space-ptb">
        <div class="container">
            <div class="row justify-content-lg-around position-relative pt-5">
                <div class="col-lg-12 col-md-12 pr-lg-5">
                    <div class="p-4 p-md-5 bg-white shadow border-radius">
                        <h4>Kami Ingin Sekali Mendengar Dari Anda</h4>
                        <form class="mt-4" id="frmQuestion">
                            {{ csrf_field() }}
                            <div class="form-group mb-3">
                                <input type="text" class="form-control" id="formName" placeholder="Name" name="formName"
                                    required>
                            </div>
                            <div class="form-group mb-3">
                                <input type="email" class="form-control" id="formEmail" name="formEmail"
                                    placeholder="Alamat Surel" required>
                            </div>
                            <div class="form-group mb-3">
                                <input type="text" class="form-control" id="formType" name="formType"
                                    placeholder="Type Pertanyaan" required>
                            </div>
                            <div class="form-group mb-4">
                                <textarea class="form-control" id="formDescription" name="formDescription" placeholder="Deskripsi Pertanyaan Anda"
                                    rows="5" required></textarea>
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
                const check = ["formName", "formType", "formEmail", "formDescription"];
                check.map(function(e) {
                    const x = $("#" + e)
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
                    $("#frmQuestion")
                        .prop("method", "post")
                        .prop("action", "{{ route('we.question') }}")
                        .submit()
                }
            })
        })

        function isEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }
    </script>
@endsection
