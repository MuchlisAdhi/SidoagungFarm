<footer class="footer footer-compact pt-4 bg-white" style="background-color: #ffffff !important;">
    <div class="footer-pad footer-compact-wrap">
        <div class="row footer-compact-content">
            <div class="col-lg-4 col-md-12">
                <div class="footer-contact-info">
                    <a href="{{ url('') }}">
                        <img class="img-fluid mb-3 footer-logo" src="{{ asset('images/saf/logo-text-big.png') }}" alt="logo">
                    </a>
                    <p class="mb-0 footer-address">Jl. Magelang - Purworejo KM 10,5 <br>Desa Sidoagung, Kec. Tempuran, <br>Kab. Magelang, Jawa Tengah, <br>Indonesia</p>
                    <a href="{{ route('we.summary') }}" class="btn btn-primary mt-3 mb-0" id="btnHubungiKami">
                        <strong>Hubungi Kami</strong>
                    </a>
                </div>
            </div>
            <div class="col-lg-8 hidden-footer-mobile">
                <div class="row footer-link-row">
                    <div class="col-lg-4 col-md-6">
                        <a href="{{ route('about-us') }}">
                            <h5 class="text-primary mb-3">Tentang Kami</h5>
                        </a>
                        <div class="footer-link">
                            <ul class="list-unstyled mb-0">
                                <li><a href="{{ route('about-us') }}#about-us">Sekilas</a></li>
                                <li><a href="{{ route('about-us') }}#news-and-csr">Berita & CSR</a></li>
                                <li><a href="https://www.product.sidoagungfarm.com/" target="_blank" rel="noopener noreferrer">Produk Pakan</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="#!">
                            <h5 class="text-primary mb-3">Lainnya</h5>
                        </a>
                        <div class="footer-link">
                            <ul class="list-unstyled mb-0">
                                <li><a href="{{ route('we.career') }}">Karir</a></li>
                                <li><a href="{{ route('we.summary') }}">Hubungi Kami</a></li>
                                <li><a href="{{ route('sitemap') }}">Sitemap</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-6">
                        <a href="#!">
                            <h5 class="text-primary mb-3">Temui Kami</h5>
                        </a>
                        <div class="footer-link">
                            <ul class="list-unstyled mb-0">
                                <li>
                                    <a href="mailto:contact@sidoagungfarm.com">
                                        <i class="fas fa-mail-bulk"></i> &nbsp; info@sidoagungfarm.com
                                    </a>
                                </li>
                                <li>
                                    <a href="tel:+62933301257">
                                        <i class="fas fa-phone" style="font-size: 20px;"></i>&nbsp;(+6293) 3301257
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.instagram.com/sidoagungfarmmagelang" target="blank">
                                        <i class="fab fa-instagram" style="font-size: 20px;"></i>&nbsp;PT. Sidoagung Farm Magelang
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom hidden-footer-mobile py-3 bg-white" style="background-color: #ffffff !important;">
        <div class="footer-pad footer-compact-wrap">
            <div class="row">
                <div class="col-lg-12 text-center text-lg-right">
                    <p class="mb-0">&copy;Copyright {{ now()->year }} <a href="{{ url('') }}"> PT. Sidoagung Farm</a><br>
                        All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
