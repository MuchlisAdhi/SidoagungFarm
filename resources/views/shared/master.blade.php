<!DOCTYPE html>
<html lang="id">
   <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
   
   <head>
      <meta charset="utf-8">
      <meta name="keywords">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="author" content="Sido Agung Group"/>	
		<meta name="description" content="Sido Agung Agro Prima Official"/>
		<meta name="keywords" content="Sido Agung Group, Sido Agung Agro Prima, Sidoagung Foods Processing, Sido Agung Farm, Sidosari Multi Farm, Asia Pangan Utama">	
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <title>PT. Sido Agung Agro Prima &#8211; &quot;Menjadi tuan rumah di negeri sendiri&quot;</title>
      <link rel="shortcut icon" href="{{ asset('images/saap/favicon.png')}}" />
      <link rel="stylesheet" href="{{ asset('css/googleapis.css?family=Archivo:400,500,600,700&amp;display=swap')}}">
      <link rel="stylesheet" href="{{ asset('css/font-awesome/all.min.css') }}" />
      <link rel="stylesheet" href="{{ asset('css/flaticon/flaticon.css') }}" />
      <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}" />
      <link rel="stylesheet" href="{{ asset('libs/bootstrap-select/css/bootstrap-select.min.css')}}">

      <link rel="stylesheet" href="{{ asset('css/owl-carousel/owl.carousel.min.css') }}" />
      <link rel="stylesheet" href="{{ asset('css/swiper/swiper.min.css') }}" />
      <link rel="stylesheet" href="{{ asset('css/animate/animate.min.css') }}" />
      <link rel="stylesheet" href="{{ asset('css/magnific-popup/magnific-popup.css') }}" />
      <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
      <link rel="stylesheet" href="{{ asset('css/custome.css') }}" />
      <link rel="stylesheet" href="{{ asset('css/mobiledevices.css')}}"/>
      <link rel="stylesheet" href="{{ asset('libs/plyr/plyr.css') }}" />
      <link rel="stylesheet" href="{{ asset('libs/venom-button/venom-button.css') }}">


      <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
      <script src="{{ asset('js/popper/popper.min.js') }}"></script>
      <script src="{{ asset('js/bootstrap/bootstrap.min.js') }}"></script>
      <script type="text/javascript" src="{{ asset('libs/venom-button/venom-button.min.js') }}"></script>
      <script>
         $.ajaxSetup({
             headers: {
                 'X-CSRF-TOKEN': "{{ csrf_token() }}"
             }
         });         
      </script>
      <style>
         #btnHubungiKami {
             background: #F80202;
             border-color: #F80202;
         }
 
         #btnHubungiKami:hover {
             background: #e74c3c;
             border-color: #e74c3c;
         }
     </style>
      @yield("css")
   </head>
   <body>
      
      <div id="myOverlay" class="overlay">
         <span class="closebtn" onclick="closeSearch()" title="Close Overlay">×</span>
         <a class="logo-search">
         <img class="img-fluid" src="{{ asset('images/sag/logo-text.png')}}" alt="logo">
         </a>
         <div class="overlay-content mt-2">
            <form class="mt-5 mb-5 ml-3" action="{{ url("") }}/id/search" method="get">
               <input type="search" class="not-click form-control-job form-control mt-4" name="keyword" placeholder="Find more...">
            </form>
         </div>
      </div>
      
      @include("shared.header")

      @yield("content")


      @include("shared.footer")

      <div id="back-to-top" class="back-to-top">atas</div>
      {{-- <div id="wa"></div> --}}
      <script src="{{ asset('libs/bootstrap-select/js/bootstrap-select.min.js')}}"></script>
      <!-- Page JS (Remove the plugin script here if site does not use that feature)-->
      <script src="{{ asset('js/horizontal-timeline/horizontal-timeline.js') }}"></script>
      <script src="{{ asset('js/owl-carousel/owl.carousel.min.js') }}"></script>
      <script src="{{ asset('js/jquery.appear.js') }}"></script>
      <script src="{{ asset('js/swiper/swiper.min.js') }}"></script>
      <script src="{{ asset('js/swiperanimation/SwiperAnimation.min.js') }}"></script>
      <script src="{{ asset('js/counter/jquery.countTo.js') }}"></script>
      <script src="{{ asset('js/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
      {{-- Template Scripts (Do not remove) --}}
      <script src="{{ asset('js/custom.js') }}"></script>
      <script src="{{ asset('libs/plyr/plyr.polyfilled.js')}}"></script>

      @yield("script")
      
      <!-- Global site tag (gtag.js) - Google Analytics -->
      {{-- <script async src="https://www.googletagmanager.com/gtag/js?id=UA-154740383-1"></script> --}}
      <script>//window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', "UA-154740383-1");</script>
      {{-- <script>
         $(function(){
            $("#wa").venomButton({
               phone: "+628119575888",
               nameClient: "Sidofoods Sales",
               headerTitle: "Commercial Sales",
               avatar: "{{asset('images/sag/avatar-wa.jpg')}}",
               buttonImage: '{{asset('assets/whatsapp.svg')}}',
               // buttonColor: "#00A651",
               // linkButton: false,
               message: "",
               chatMessage: 'Salam Hangat,<br/>Sidofoods disini.<br/>Apa yang bisa kami bantu ?',
               showPopup: true,
               position: "right",
               headerColor: '#008641',
            });
         })
      </script> --}}
   </body>
</html>