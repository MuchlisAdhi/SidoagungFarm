<div class="modal fade" id="modalRespons" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
       <div class="modal-content">
          <div class="modal-header" style="border: none;">
             <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: relative;right: 20px;">
             <span aria-hidden="true" style="font-size: 2rem;">×</span>
             </button>
          </div>
          <div class="modal-body">
             <div class="row">
                <div class="col-sm-9 align-self-start align-self-lg-center ">
                   <p class="d-flex align-items-center mb-4">
                      <span class="font-weight-bold text-primary mr-2" id="teks_title">Thank you for your time, partner!</span>
                   </p>
                   <h5 class="mb-4 text-primary" id="teks_1">
                      {{ session('success') ?? 'We will get back to you as soon as possible.' }}
                   </h5>
                   <p class="mb-4 text-primary" id="teks_2">Let’s create many great stories!</p>
                   <p class="mb-4 text-primary" id="teks_3">Sincerely,</p>
                   <img class="img-fluid" src="{{ asset('images/saf/logo-horizontal.png') }}" alt="">
                </div>
                <div class="col-sm-3 align-self-start align-self-lg-center ">
                   <img class="img-fluid " src="{{ asset('images/ai/telur.png') }}" alt="" style="position: absolute; top:10px; right: 10px;">
                </div>
             </div>
          </div>
       </div>
    </div>
 </div>
