@extends('shared.master')
@section('body_class', '')

@section('content')
<x-banner-summary mode="career"></x-banner-summary>
<section class="space-ptb background-sidoagung">
   <div class="container">
     <div class="row justify-content-center">
       <div class="col-lg-12 pb-lg-0">
         <div class="section-title mb-3 pt-3">
           <h2 class="text-white">Bergabung Bersama Kami</h2>
         </div>
         <p class="text-white">Di PT. Sidoagung Farm, Kami Menyediakan Lingkungan Kerja Yang Sehat, Hangat Dan Membangun. Anda Bisa mengembangkan Karir Anda Bersama Kami Dalam Kenyamanan Yang Kompetitif.</p>
       </div>
     </div>
   </div>
 </section>

<div class="container">
   <div class="row">
      <div class="col-lg-8">
         <h4 class="mt-180-article">
            Berikut daftar pekerjaan kami yang tersedia saat ini
         </h4>
      </div>
      <div class="col-lg-4">
         <form class="mt-4 ml-3" method="get" id="frmFindJob">
             <input type="text" class="not-click form-control" name="keyword" placeholder="Posisi yang dicari..">
             <button id="btn-color-search-job" class="button-search mt-2" type="submit" style="margin-right: -10px;"> Search </button>
         </form>
     </div>
   </div>
   <div class="row">
      <div class="col-lg-12">
         @foreach($list as $l)
            <a href="{{route('we.career', ["id" => encrypt($l->id)])}}" class="row list-job bg-white border-radius mt-3 surface-contrast surface-strong surface-contrast-hover">
               <div class="col-md-8">
                  <div class="circle-text"></div>
                  <div class="pl-5 pb-5 pt-5 ">
                     <p class="mb-2">Jabatan</p>
                     <h5 class="text-primary mb-2">{{$l->position}}</h5>
                     <p class="text-light for-mobile-text">Lokasi : {{$l->location}}</p>
                  </div>
               </div>
               <div class="col-md-3 for-mobile-text-hidden">
                  <div class="pb-5 pt-5 ">
                     <p class="mb-2 text-light">Lokasi</p>
                     <h6 class="text-light mb-2">{{$l->location}}</h6>
                  </div>
               </div>
               <div class="col-md-1 view-next-go for-mobile-text-hidden">
                  <center>
                     <i class="fas fa-chevron-right i-job-icon" style="color: #008641;"></i>
                  </center>
               </div>
            </a>
            @endforeach
      </div>
   </div>

   <div class="row">
      <div class="col-lg-12">
         <center>
            <div class="pagination p1">
               <ul>
                  @for($i = 0; $i < $total; $i++)
                     @if($i + 1 == $current)
                        <a class="is-active" href="#!">
                           <li>{{ $i + 1 }}</li>
                        </a>
                     @else
                        <a href="{{url()->current()}}?page={{$i+1}}">
                           <li>{{$i+1}}</li>
                        </a>
                     @endif
                  @endfor
                  {{-- <a href="{{url()->current()}}?page=2">
                     <li><i class="fas fa-caret-right"></i></li>
                  </a> --}}
               </ul>
            </div>
         </center>
      </div>
   </div>
</div>

@endsection
