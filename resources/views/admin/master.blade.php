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

      @yield('style')
   </head>
   <body class="hold-transition skin-green-light sidebar-mini">
      <div class="wrapper">
         <header class="main-header">
            <a href="{{ route('admin.main') }}" class="logo">
            <span class="logo-mini"><b>SSAP</b></span>
            <span class="logo-lg"><b>Sido</b>Agung</span>
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
                        <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="user-image" alt="User Image">
                        <span class="hidden-xs">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                           <li class="user-header">
                              <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle"
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
        $(document).ready(function(){ 
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
