<!DOCTYPE html>
<html lang="id">
   <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
   
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
      <meta name="author" content="Sido Agung Group"/>
		<meta name="description" content="@yield('meta_description', 'PT. Sidoagung Farm adalah perusahaan pakan ternak berkualitas di Indonesia, bagian dari Sido Agung Group.')"/>
		<meta name="keywords" content="Sido Agung Group, Sidoagung Farm, Sidoagung Foods Processing, Sido Agung Farm, Sidosari Multi Farm, Asia Pangan Utama">
      <meta name="robots" content="index,follow,max-image-preview:large">
      @php
         $canonicalPath = request()->path();
         $canonicalPath = $canonicalPath === '/' ? '' : '/' . ltrim($canonicalPath, '/');
         $canonicalDefault = rtrim((string) config('app.url'), '/') . $canonicalPath;
      @endphp
      <meta property="og:type" content="website">
      <meta property="og:title" content="@yield('meta_title', 'PT. Sidoagung Farm - Menjadi tuan rumah di negeri sendiri')">
      <meta property="og:description" content="@yield('meta_description', 'PT. Sidoagung Farm adalah perusahaan pakan ternak berkualitas di Indonesia, bagian dari Sido Agung Group.')">
      <meta property="og:url" content="@yield('canonical_url', $canonicalDefault)">
      <meta property="og:image" content="{{ asset('images/saf/logo.png') }}">
      <meta name="twitter:card" content="summary_large_image">

      <meta name="csrf-token" content="{{ csrf_token() }}" />
      <title>@yield('meta_title', 'PT. Sidoagung Farm - Menjadi tuan rumah di negeri sendiri')</title>
      <link rel="canonical" href="@yield('canonical_url', $canonicalDefault)" />
      <link rel="shortcut icon" href="{{ asset('images/saf/favicon.png')}}" />

      @php
         $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'PT. Sidoagung Farm',
            'url' => url('/'),
            'logo' => asset('images/saf/logo.png'),
            'contactPoint' => [[
               '@type' => 'ContactPoint',
               'telephone' => '+62-933-3301257',
               'contactType' => 'customer service',
               'areaServed' => 'ID',
               'availableLanguage' => ['Indonesian', 'English'],
            ]],
         ];

         $websiteSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'PT. Sidoagung Farm',
            'url' => url('/'),
            'inLanguage' => 'id-ID',
         ];
      @endphp
      <script type="application/ld+json">
         {!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
      </script>
      <script type="application/ld+json">
         {!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
      </script>
      @yield('json_ld')

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
   <body class="@yield('body_class')">
      
      <div id="myOverlay" class="overlay">
         <span class="closebtn" onclick="closeSearch()" title="Close Overlay">×</span>
         <a class="logo-search">
         <img class="img-fluid" src="{{ asset('images/saf/logo-horizontal.png')}}" alt="logo">
         </a>
         <div class="overlay-content mt-2">
            <form class="mt-5 mb-5 ml-3" action="{{ url("") }}/id/search" method="get">
               <input type="search" class="not-click form-control-job form-control mt-4" name="keyword" placeholder="Find more...">
            </form>
         </div>
      </div>
      
      @include("shared.header")

      <main class="@yield('main_class', 'bg-light')">
         @yield("content")
      </main>


      <div class="site-footer-shell bg-white">
         @include("shared.footer")
      </div>

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
