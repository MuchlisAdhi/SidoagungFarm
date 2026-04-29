<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>PT. Sidoagung Farm</title>
      <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
      <link rel="shortcut icon" href="{{ asset('images/saf/favicon.png') }}">
      <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
      <link rel="stylesheet" href="{{ asset('fonts/font-awesome/css/font-awesome.min.css') }}">
      <link rel="stylesheet" href="{{ asset('fonts/ionicons/ionicons.min.css') }}">
      <link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.min.css') }}">
      <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">
      <link rel="stylesheet" href="{{ asset('plugins/toast/jquery.toast.min.css') }}">
      <style>
         .admin-loading-overlay {
            position: fixed;
            inset: 0;
            z-index: 99999;
            background: rgba(255, 255, 255, 0.72);
            display: none;
         }

         .admin-loading-wrap {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
         }

         .admin-snake-loader {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 4px solid rgba(0, 166, 81, 0.2);
            border-top-color: #00A651;
            border-right-color: #00A651;
            animation: adminSnakeSpin 0.85s linear infinite;
            margin: 0 auto;
         }

         .admin-loading-text {
            margin-top: 10px;
            color: #00A651;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.2px;
         }

         .btn-loading-disabled,
         .btn-loading-disabled:hover,
         .btn-loading-disabled:focus {
            opacity: 0.7 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
         }

         @keyframes adminSnakeSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
         }
      </style>

      @yield('style')
   </head>
   <body class="hold-transition skin-green-light sidebar-mini">
      <div class="wrapper">
         <header class="main-header">
            <a href="{{ route('admin.main') }}" class="logo">
            <span class="logo-mini"><b>SAF</b></span>
            <span class="logo-lg"><b>Sidoagung</b></span>
            </a>
            <nav class="navbar navbar-static-top" role="navigation">
               <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               </a>
               <div class="navbar-custom-menu">
                  <ul class="nav navbar-nav">
                     <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ asset('dist/img/saf-160x160.jpg') }}" class="user-image" alt="User Image">
                        <span class="hidden-xs">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                           <li class="user-header">
                              <img src="{{ asset('dist/img/saf-160x160.jpg') }}" class="img-circle"
                                 alt="User Image">
                              <p>
                                 {{ auth()->user()->name }}
                              </p>
                           </li>
                           <li class="user-footer">
                              <div class="pull-left">
                                 <a href="#" class="btn btn-default btn-flat">Change Password</a>
                              </div>
                              <div class="pull-right">
                                 <a href="{{ route('logout') }}" class="btn btn-default btn-flat">Sign out</a>
                              </div>
                           </li>
                        </ul>
                     </li>
                  </ul>
               </div>
            </nav>
         </header>
         <aside class="main-sidebar">
            <section class="sidebar">
               <ul class="sidebar-menu">
                  <li class="header">NAVIGATION</li>
                  @php
                     $menus = app(\App\Services\Contracts\INavigationService::class)
                        ->GetAccessNavigation(auth()->user());
                  @endphp
                  @foreach ($menus as $menu)
                     @if (count($menu['childs']))
                        <li class="treeview">
                           <a href="#">
                              <i class="{{ $menu['icon'] ?? 'fa fa-home' }}"></i>
                              <span>{{$menu["title"]}}</span>
                              <i class="fa fa-angle-left pull-right"></i>
                           </a>
                           <ul class="treeview-menu">
                              @foreach ($menu['childs'] as $child)
                                 <li>
                                    <a href="{{$child['url'] ?? '#'}}">
                                       <i class="fa fa-angle-double-right"></i> {{$child['title']}}
                                    </a>
                                 </li>
                              @endforeach
                           </ul>
                        </li>
                     @else
                        <li>
                           <a href="{{$menu['url'] ?? '#'}}">
                              <i class="{{ $menu['icon'] ?? 'fa fa-angle-double-right' }}"></i> {{$menu['title']}}
                           </a>
                        </li>
                     @endif
                  @endforeach
               </ul>
            </section>
         </aside>
         <div class="content-wrapper">
            <section class="content-header">
               <h1>
                @yield("page")
               </h1>
            </section>
            <section class="content">
               
               @yield('content')
              
            </section>
         </div>
         <div id="adminLoadingOverlay" class="admin-loading-overlay" aria-hidden="true">
            <div class="admin-loading-wrap">
               <div class="admin-snake-loader"></div>
               <div class="admin-loading-text">Menyimpan...</div>
            </div>
         </div>
      </div>
      <script src="{{ asset('plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
      <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
      <script src="{{ asset('plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
      <script src="{{ asset('plugins/fastclick/fastclick.min.js') }}"></script>
      <script src="{{ asset('plugins/toast/jquery.toast.min.js') }}"></script>
      <script src="{{ asset('dist/js/app.min.js') }}"></script>
      <script src="{{ asset('dist/js/demo.js') }}"></script>
      <script>
         $.ajaxSetup({
             headers: {
                 'X-CSRF-TOKEN': "{{ csrf_token() }}"
             }
         });

        (function(w, $) {
            function resolveButton(button) {
               if (!button) {
                  return $();
               }

               if (typeof button === 'string') {
                  return $(button).first();
               }

               if (button.jquery) {
                  return button.first();
               }

               return $(button).first();
            }

            function setOverlay(show) {
               if (show) {
                  $("#adminLoadingOverlay").stop(true, true).fadeIn(120);
                  return;
               }

               $("#adminLoadingOverlay").stop(true, true).fadeOut(120);
            }

            w.AdminSubmit = {
               start: function(button, text) {
                  var $btn = resolveButton(button);
                  if ($btn.length) {
                     if ($btn.data("loadingLocked")) {
                        return false;
                     }

                     $btn.data("loadingLocked", true);
                     $btn.data("loadingHtml", $btn.html());
                     $btn.prop("disabled", true).addClass("btn-loading-disabled");

                     if (text) {
                        $btn.html('<i class="fa fa-spinner fa-spin"></i> ' + text);
                     }
                  }

                  setOverlay(true);
                  return true;
               },
               stop: function(button) {
                  var $btn = resolveButton(button);
                  if ($btn.length) {
                     var prevHtml = $btn.data("loadingHtml");
                     if (typeof prevHtml !== "undefined") {
                        $btn.html(prevHtml);
                     }

                     $btn.removeData("loadingHtml")
                        .removeData("loadingLocked")
                        .prop("disabled", false)
                        .removeClass("btn-loading-disabled");
                  }

                  setOverlay(false);
               },
               stopAll: function() {
                  $(".btn-loading-disabled").each(function() {
                     var $btn = $(this);
                     var prevHtml = $btn.data("loadingHtml");
                     if (typeof prevHtml !== "undefined") {
                        $btn.html(prevHtml);
                     }

                     $btn.removeData("loadingHtml")
                        .removeData("loadingLocked")
                        .prop("disabled", false)
                        .removeClass("btn-loading-disabled");
                  });

                  $("form").removeData("submitLocked");
                  setOverlay(false);
               }
            };

            $(w).on("pageshow", function() {
               w.AdminSubmit.stopAll();
            });

            $(document).on("submit", "form", function(e) {
               var $form = $(this);
               var method = ($form.attr("method") || "").toLowerCase();

               if (method !== "post") {
                  return true;
               }

               if ($form.data("submitLocked")) {
                  e.preventDefault();
                  return false;
               }

               $form.data("submitLocked", true);

               var $submit = $form.find('button[type="submit"], input[type="submit"]').filter(":enabled").first();
               if ($submit.length) {
                  w.AdminSubmit.start($submit, "Menyimpan...");
                  return true;
               }

               setOverlay(true);
               return true;
            });
        })(window, jQuery);

        $(document).ready(function(){ 
            @if ($errors->any())
               @foreach ($errors->all() as $errorMessage)
                  $.toast({
                     heading: 'Error',
                     text: @json($errorMessage),
                     showHideTransition: 'fade',
                     position: 'bottom-right',
                     icon: 'error'
                  })
               @endforeach
            @endif

            @if (session()->has("error"))
               $.toast({
                  heading: 'Error',
                  text: '{{session()->get("error")}}',
                  showHideTransition: 'fade',
                  position: 'bottom-right',
                  icon: 'error'
               })
            @endif

            @if (session()->has("success")) 
               $.toast({
                  heading: 'Success',
                  text: '{{session()->get("success")}}',
                  showHideTransition: 'slide',
                  position: 'bottom-right',
                  icon: 'success'
               })
            @endif
        });      
      </script>
      @yield('script')
   </body>
</html>
