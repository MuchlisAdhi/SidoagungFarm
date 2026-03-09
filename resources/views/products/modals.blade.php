<div class="modal fade" id="inquiryproduct" tabindex="-1" role="dialog" aria-labelledby="inquiryproduct"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="border: none;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="position: relative;right: 20px;">
                    <span aria-hidden="true" style="font-size: 2rem;">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 align-self-start align-self-lg-center ">
                        <img class="img-fluid border-radius" id="img_product"
                            src="" alt="" style="width: 580px;">
                    </div>
                    <div class="col-md-6 align-self-start align-self-lg-center ">
                        <div class="p-4 p-md-5 bg-white border-radius">
                            <img class="img-fluid pb-3" src="{{ asset('images/saap/logo-text.png') }}" alt="logo" style="width: 250px;">
                            <h3>Ada pertanyaan?</h3>
                            <form class="mt-4" action="!#" method="POST">
                                {{ csrf_field() }}
                                <div class="form-group mb-3">
                                    <input type="text" class="form-control" id="formInputName" placeholder="Masukan Nama" required>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="text" class="form-control" id="formInputEmail" placeholder="Alamat Email" >
                                </div>
                                <div class="form-group mb-3">
                                    <input type="tel" class="form-control" id="formInputPhone" placeholder="Nomor Hp">
                                </div>
                                <div class="form-group mb-4">
                                    <textarea class="form-control" id="formInputDescription" placeholder="Deskripsi Permintaan" rows="3"></textarea>
                                </div>
                                <div style="float:right;" class="form-group mb-0">
                                    <span id="errorMessage" class="text-danger"></span>
                                    <button type="button" id="btnOrder" class="btn btn-danger text-white">Kirim Pesan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalRespons" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="border: none;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="position: relative;right: 20px;">
                    <span aria-hidden="true" style="font-size: 2rem;">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-9 align-self-start align-self-lg-center ">
                        <p class="d-flex align-items-center mb-4">
                            <span class="font-weight-bold text-primary mr-2" id="teks_title">Thank you for your time,
                                partner!</span>
                        </p>
                        <h5 class="mb-4 text-primary" id="teks_1">We will get back to you as soon as possible.</h5>
                        <p class="mb-4 text-primary" id="teks_2">Let’s create many great stories!</p>
                        <p class="mb-4 text-primary" id="teks_3">Sincerely,</p>
                        <img class="img-fluid" src="{{ asset('images/sag/logo-text.png') }}" alt="" style="width: 250px;">
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