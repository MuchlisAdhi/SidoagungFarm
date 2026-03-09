<footer class="footer pt-4" style="background-color: #00A651; color: white;">
    <div class="footer-pad">
        <div class="row justify-content-md-center mt-4">
            <div class="col col-lg-2 text-center">
                <a href="{{ url('') }}">
                    <img class="img-fluid mb-4" 
                        src="{{ asset('images/saap/logo-text-white.png') }}" 
                        alt="Sido Agung Agro Prima"
                        title="Sido Agung Agro Prima"
                        style="width: 200px;">
                </a>
            </div>
            <div class="col-lg-1">
                &nbsp;
            </div>
            <div class="col col-lg-2">
                <div class="footer-link">
                    <ul class="list-unstyled mb-0">
                        <li><a href="{{ route('about-us') }}#about-us" class="text-white font-weight-bold p-1" style="font-size: 14px">Tentang Kami</a></li>
                        <li><a href="{{ route('csr.news') }}" class="text-white font-weight-bold p-1" style="font-size: 14px">Berita & CSR</a></li>
                        <li><a href="{{ route('products') }}" class="text-white font-weight-bold p-1" style="font-size: 14px">Produk Pakan</a></li>
                    </ul>
                </div>
            </div>
            <div class="col col-lg-2">
                <div class="footer-link">
                    <ul class="list-unstyled mb-0">
                        <li><a href="{{ route('we.career') }}" class="text-white font-weight-bold p-1" style="font-size: 14px">Karir</a></li>
                        <li><a href="{{ route('we.summary') }}" class="text-white font-weight-bold p-1" style="font-size: 14px">Hubungi Kami</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="mailto:contact@sidoagungagunggroup.com" class="text-white">
                    <span class="">
                        <i class="fas fa-mail-bulk"></i> &nbsp; info@sidoagungfeed.com
                    </span>
                </a>

                <br />
                <a href="tel:+6282125998700" class="text-white">
                    <span class="">
                        <i class="fas fa-phone"></i> &nbsp;(+62) 821-2599-8700
                    </span>
                </a>
                <br />
                <span class="text-white">
                    Jl. Raya Cirebon  - Losari KM.16, Desa Bendungan, Kec. Pangenan, Cirebon, Jawa Barat
                </span>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12 text-center">
                <span class="text-white" style="font-size: 11px;">
                    <p class="mb-0">©Copyright {{ now()->year }} <a href="{{ url('') }}" class="text-white"><strong>PT. Sido Agung Agro Prima</strong></a>
                    
                    All Rights Reserved.
                </p>
                </span>
            </div>
        </div>
    </div>
    {{-- 
    <div class="footer-pad">
        <div class="row">
            <div class="col-lg-3 col-md-12">
                <div class="footer-contact-info">
                    <a href="{{ url('') }}">
                        <img class="img-fluid mb-4" src="{{ asset('images/saap/logo-text-white.png') }}" alt="logo"
                            style="width: 200px;">
                    </a>
                    <p class="mb-2">Jl. H. Hasan Arif Sukaseneng, Kec. Banyuresmi Garut, Jawa Barat Indonesia</p>
                    <br>
                    <a href="{{ route('we.summary') }}" class="btn btn-primary mt-2 mb-5" id="btnHubungiKami">
                        <strong> Hubungi Kami </strong>
                    </a>
                </div>
            </div>
            <div class="row hidden-footer-mobile col-lg-9">
                <div class="col-lg-3 col-md-6 mt-4 ml-3 pt-4 mt-lg-0">
                    <a href="{{ route('about-us') }}">
                        <h5 style="font-size:18px;" class="text-primary mb-2 mb-sm-4">Tentang Kami</h5>
                    </a>
                    <div class="footer-link">
                        <ul class="list-unstyled mb-0">
                            <li><a href="{{ route('about-us') }}#about-us">Sekilas</a></li>
                            <li><a href="{{ route('about-us') }}#news-and-csr">Berita & CSR</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mt-4 ml-3 pt-4 mt-lg-0">
                    <a href="{{ route('products') }}">
                        <h5 style="font-size:18px;" class="text-primary mb-2 mb-sm-4">Produk</h5>
                    </a>
                    <div class="footer-link">
                        <ul class="list-unstyled mb-0">
                            <li><a href="{{ route('products') }}?category=karkas">Karkas</a></li>
                            <li><a href="{{ route('products') }}?category=boneless">Boneless</a></li>
                            <li><a href="{{ route('products') }}?category=trimming">Trimming</a></li>
                            <li><a href="{{ route('products') }}?category=sampingan">Sampingan</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6 mt-4 ml-3 pt-4 mt-lg-0">
                    <a href="#!">
                        <h5 style="font-size:18px;" class="text-primary mb-2 mb-sm-4">Lainnya</h5>
                    </a>
                    <div class="footer-link">
                        <ul class="list-unstyled mb-0">
                            <li><a href="{{ route('csr.resep') }}">Resep</a></li>
                            <li><a href="{{ route('we.career') }}">Karir</a></li>
                            <li><a href="{{ route('we.summary') }}">Hubungi Kami</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mt-4 ml-3 pt-4 mt-lg-0">
                    <a href="#!">
                        <h5 style="font-size:18px;" class="text-primary mb-2 mb-sm-4">Temui Kami</h5>
                    </a>
                    <div class="footer-link">
                        <ul class="list-unstyled mb-0">
                            <li>
                                <a href="mailto:contact@sidofoods.com">
                                    <i class="fas fa-mail-bulk" style="font-size: 20px;"></i>&nbsp;contact@sidofoods.com
                                </a>
                            </li>
                            <li>
                                <a href="tel:+628119575888">
                                    <i class="fas fa-phone" style="font-size: 20px;"></i>&nbsp;(+62) 8119575888
                                </a>
                            </li>
                            <li>
                                <a href="https://www.instagram.com/sidofoods" target="blank">
                                    <i class="fab fa-instagram" style="font-size: 20px;"></i>&nbsp;SidoFoods
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> 
    </div>--}}
    {{-- <div class="footer-bottom hidden-footer-mobile  py-4">
        <div class="footer-pad">
            <div class="row ">
                <div class="col-lg-8 text-center text-lg-left mb-3 mb-lg-0">
                    &nbsp;
                </div>
                <div class="col-lg-4 text-center text-lg-right">
                    <p class="mb-0">©Copyright {{ now()->year }} <a href="{{ url('') }}"> PT. Sidoagung
                            Foods Processing</a><br>
                        All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </div> --}}
</footer>
