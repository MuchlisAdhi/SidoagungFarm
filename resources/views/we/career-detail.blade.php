@extends('shared.master')
@section('body_class', 'force-sticky-header')

@section('content')
<x-banner-summary mode="career"></x-banner-summary>

<section class="header-farmingnew bg-white">
   <div class=" topdown">
      <center style="height: 1px;">
          <img class="topdown-img" src="{{asset('images/ai/arrowupbutton.png')}}">
      </center>
  </div>

  <div class="container">
      <div class="row align-items-center">
         <div class="col-xl-12">
            <div class="blog-detail surface-contrast surface-strong surface-contrast-padded">
               <div class="blog-post mb-4 ">
                  <div class="blog-post-content">
                      <div class="blog-post-details jobs-info-positions">
                          <div class="blog-post-info">
                          <div>
                              <a class="text-light" href="#">Position</a>
                          </div>
                          </div>
                          <h2 class="blog-post-title">
                           {{$rs->position}}
                          </h2>
                      </div>
                      <div class="blog-post-info">
                          <div>
                          <a class="text-light" href="#">Location</a>
                          <h5 class="text-primary" href="#">{{$rs->location}}</h5>
                          </div>
                          <div style="float: right;position: absolute;right: 0;">
                          <a class="text-light" href="#">Posted on</a>
                          <p class="text-primary" href="#">{{date("d M Y", strtotime($rs->postedon))}}</p>
                          </div>
                      </div>
                      <hr>
                  </div>
              </div>
              <div class="blog-post mb-4 ">
                  <div class="blog-post-content">
                      <div class="blog-post-details">
                          <h6 class="blog-post-title">
                          Description
                          </h6>
                          <p class="mb-4">
                           {!! $rs->description !!}
                           {{-- <p style="list-style-type: none; padding-left: 1.5em;">- Proficient in SQL</p> --}}
                          </p>
                          <h6 class="blog-post-title">
                          Qualifications
                          </h6>
                          <p class="mb-4">
                           {!! $rs->qualification !!}
                          </p>
                      </div>
                      <a class="btn btn-block btn-primary" href="{{route('we.career.apply', ["id" => encrypt($rs->id)])}}">Apply for this position</a>
                  </div>
              </div>
            </div>
         </div>
      </div>
  </div>
</section>


@endsection
