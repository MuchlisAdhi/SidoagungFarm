<style>
   #modalRespons {
      z-index: 11050;
   }

   #modalRespons .modal-dialog {
      max-width: 640px;
   }

   #modalRespons .modal-content {
      border: none;
      border-radius: 14px;
      box-shadow: 0 14px 34px rgba(2, 45, 98, 0.16);
   }

   #modalRespons .modal-header {
      border: none;
      padding: 0.75rem 0.75rem 0;
   }

   #modalRespons .modal-header .close {
      position: static;
      margin-left: auto;
      opacity: 0.8;
   }

   #modalRespons .modal-body {
      padding: 1.25rem 1.5rem 1.5rem;
   }

   #modalRespons .modal-art {
      position: absolute;
      top: 10px;
      right: 10px;
   }

   @media (max-width: 767.98px) {
      #modalRespons .modal-dialog {
         margin: 0.75rem auto;
         max-width: calc(100% - 24px);
      }

      #modalRespons .modal-body {
         padding: 1rem;
      }

      #modalRespons .modal-body .row {
         display: block;
      }

      #modalRespons .modal-art-wrap {
         display: none;
      }

      #modalRespons #teks_title,
      #modalRespons #teks_1 {
         font-size: 1rem;
      }
   }
</style>

<div class="modal fade" id="modalRespons" tabindex="-1" role="dialog" aria-labelledby="modalResponsTitle" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true" style="font-size: 2rem;">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-sm-9 align-self-start align-self-lg-center">
                  <p class="d-flex align-items-center mb-4">
                     <span class="font-weight-bold text-primary mr-2" id="teks_title">Thank you for your time, partner!</span>
                  </p>
                  <h5 class="mb-4 text-primary" id="teks_1">
                     {{ session('success') ?? 'We will get back to you as soon as possible.' }}
                  </h5>
                  <p class="mb-4 text-primary" id="teks_2">Let's create many great stories!</p>
                  <p class="mb-4 text-primary" id="teks_3">Sincerely,</p>
                  <img class="img-fluid" src="{{ asset('images/saf/logo-horizontal.png') }}" alt="">
               </div>
               <div class="col-sm-3 align-self-start align-self-lg-center modal-art-wrap">
                  <img class="img-fluid modal-art" src="{{ asset('images/ai/telur.png') }}" alt="">
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
