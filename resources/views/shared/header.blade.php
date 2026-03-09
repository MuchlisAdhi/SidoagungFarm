<header class="header sticky">
    <nav class="navbar bg-white navbar-static-top navbar-expand-lg">
        <div class="container-fluid">
            <button type="button" class="navbar-toggler right-up-collapse" data-trigger="#navbar_main" style="right: 25px!important">
                <i class="fas fa-align-left"></i>
            </button>
            <a class="navbar-brand" href="{{url("")}}">
                <img class="img-fluid" src="{{ asset('images/saap/logo-text.png') }}" alt="Sido Agung Agro Prima" title="Sido Agung Agro Prima">
            </a>
            <div class="navbar-collapse collapse" id="navbar_main">
                <ul class="nav navbar-nav ml-auto mr-5">
                    <li class="nav-item ">
                        <a href="{{route('about-us')}}" class="nav-link">Tentang Kami</a>
                    </li>
                    <li class="nav-item ">
                        <a href="{{ route('csr.news') }}" class="nav-link">Berita & CSR</a>
                    </li>
                    <li class="nav-item ">
                        <a href="{{route('products')}}" class="nav-link">Produk Pakan</a>
                    </li>
                    <li class="nav-item ">
                        <a href="{{route('we.career')}}" class="nav-link">Karir</a>
                    </li>
                    <li class="nav-item ">
                        <a href="{{route('we.summary')}}" class="nav-link">Hubungi Kami</a>
                    </li>
                    
                </ul>
            </div>
        </div>
    </nav>
</header>