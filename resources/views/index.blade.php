@extends('shared.master')

@php
    $categories = [
        'karkas'        => "Karkas",
        'boneless'     => "Boneless",
        'trimming'     => "Trimming",
        'sampingan'    => "Sampingan"
    ];
@endphp

@section('content')
    <x-banner-summary mode="beranda"></x-banner-summary>

    {{-- <section class="pt-4 pb-4" style="background-color: #00A651;">
        <div class="container ">
            <div class="row justify-content-center">
                <span class="h3 text-white">Produk Kami</span>
            </div>
            <div class="row">
                <div class="col-lg-12" style="padding: 0px;">
                    <div id="carousel" class="carousel slide carousel-fade" data-ride="carousel" data-interval="6000">
                        <div class="carousel-inner" role="listbox">
                            @foreach ($banners as $banner)
                                <div class="carousel-item {{ $loop->index == 0 ? 'active' : '' }}"
                                    style="z-index: unset;">
                                    <img src="{{ route('main.getResource', ['id' => $banner->mediaId]) }}" class="w-100"
                                        alt="{{ $banner->title }}" />
                                </div>
                            @endforeach

                        </div>
                        <a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pt-4 pb-4">
        <div class="container ">
            <div class="row justify-content-center">
                <span class="h3">Komitmen Untuk Anda</span>
            </div>
            <div class="row">
                <div class="col-lg-12" style="padding: 0px;">
                    <div class="card-carousel">
                        <div class="my-card">
                            <div class="row h-100">
                                <div class="col-lg-12 text-center my-auto text-white">
                                    <img src="{{ asset('images/sag/asuh/aman.png') }}" />
                                    <div class="mt-3"><span class="h4 text-white">Aman</span></div>
                                    <div><span>Tidak Mengandung Bibit Penyakit Dan Bahan Kimia Ataupun Obat-obatan Yang
                                            Dapat Mengganggu Kesehatan.</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="my-card">
                            <div class="row h-100">
                                <div class="col-lg-12 text-center my-auto text-white">
                                    <img src="{{ asset('images/sag/asuh/sehat.png') }}" />
                                    <div class="mt-3"><span class="h4 text-white">Sehat</span></div>
                                    <div><span>Memiliki Zat-zat Yang Bergizi Dan Berguna Bagi Kesehatan Dan
                                            Pertumbuhan.</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="my-card">
                            <div class="row h-100">
                                <div class="col-lg-12 text-center my-auto text-white">
                                    <img src="{{ asset('images/sag/asuh/asuh.png') }}" />
                                    <div class="mt-3"><span class="h4 text-white">ASUH</span></div>
                                    <div><span>Komitmen Kami Memberikan Kualitas Terbaik Bagi Konsumen.</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="my-card">
                            <div class="row h-100">
                                <div class="col-lg-12 text-center my-auto text-white">
                                    <img src="{{ asset('images/sag/asuh/utuh.png') }}" />
                                    <div class="mt-3"><span class="h4 text-white">Utuh</span></div>
                                    <div><span>Tidak Dicampur Dengan Bagian Lain Dari Hewan Lain.</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="my-card">
                            <div class="row h-100">
                                <div class="col-lg-12 text-center my-auto text-white">
                                    <img src="{{ asset('images/sag/asuh/halal.png') }}" />
                                    <div class="mt-3"><span class="h4 text-white">Halal</span></div>
                                    <div><span>Dipotong Dan Ditangani Sesuai Dengan Syariat Agama Islam.</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pt-4 pb-4">
        <div class="container ">
            @php
                $newResep = $resep->first();
                $row = 0;
            @endphp
            <div class="row">
                @if($newResep)
                <div class="col-lg-6">
                    <img class="img-fluid" src="{{ route('main.getResource', ['id' => $newResep->thumbnail]) }}" alt="">
                    <div class="mt-2">
                        <span class="h6">Resep Terbaru</span>
                    </div>
                    <div class="mt-1">
                        <span class="h4">{{$newResep->title}}</span>
                    </div>
                </div>
                @endif
                <div class="col-lg-6">
                    @foreach($resep as $r)
                        @if($loop->index > 0)
                            @php
                                $cls = $loop->index > 1 ? "row mt-180-article" : "row";
                            @endphp
                            <div class="{{$cls}}">
                                <div class="col-lg-5">
                                    <img class="img-fluid" src="{{ route('main.getResource', ['id' => $r->thumbnail]) }}"
                                        alt="">
                                </div>
                                <div class="col-lg-7">
                                    <div class="mt-2">
                                        <span class="h5">{{$r->title}}</span>
                                    </div>
                                    <div class="mt-1 text-justify">
                                        <span class="h7">{!! Str::limit($r->content, 110, "...") !!}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="pt-4 pb-4" style="background-color: #00A651;">
        <div class="container ">
            <div class="row justify-content-center">
                <span class="h3 text-white">Testimoni</span>
            </div>
            <div class="row">
                @foreach($testimoni as $t)
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-5">
                                <img class="imageblock-section-img img-fluid" src="{{ route('main.getResource', ['id' => $t->photo]) }}" alt="" style="border-radius: 20px;">
                            </div>
                            <div class="col-lg-7">
                                <div>
                                    <div class="mt-2"><span class="h4 text-white">{{$t->name}}</span></div>
                                    <div><span class="h5 text-white font-italic" style="border-bottom: 3px solid #F80202;">
                                        {{$t->title}}</span></div>
                                    <div class="mt-1"><span class="text-white">{{$t->testimoni}}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section> --}}

    {{-- <section class="pt-4 pb-4" style="background-color: #00A651;">
        <div class="container ">
            <div class="row justify-content-center">
                <span class="h3 text-white">Produk Kami</span>
            </div>
            <div class="row">
                <div id="productsect" class="row bg-white">
                    <div class="col-md-12">
                        <div class="owl-carousel text-left" data-nav-arrow="true" data-nav-dots="true" data-items="1"
                            data-md-items="1" data-sm-items="1" data-xs-items="1" data-xx-items="1" style="z-index: unset;">
                            @foreach ($products as $list)
                                <div class="items">
                                    <section class=" bg-white">
                                        <div class="container">
                                            <div class="row justify-content-center">
                                                @foreach ($list as $p)
                                                    <div onclick="openForm('{{ encrypt($p->id) }}')"
                                                        class="col-lg-3 col-md-6 text-center mobile-product mb-4">
                                                        <div class="p-2 d-inline-block border-radius bg-product p-3 ">
                                                            <div class="product-imagego"
                                                                style="background: url('{{ route('main.getResource', ['id' => $p->mediaId]) }}');">
                                                            </div>
                                                            <br>
                                                            <a href="javascript:void(0);"
                                                                onclick="openForm('{{ encrypt($p->id) }}')"
                                                                class="mb-4 mt-3 product-text">{{ $p->title }}</a>
                                                            <br>
                                                            <small>{{ $p->description }}</small>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </section>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    {{-- <section class="space-ptb ">
        <div class="container">
            <div class="row bg-white">

            </div>
        </div>
    </section> --}}
    @php
        $slides = [
            [
                'title' => "Kapasitas",
                'content'   => "Feedmill milik PT. Sidoagung Farm memiliki kapasitas 25.000 ton per bulan, hal ini menjadi komitmen kami untuk melakukan pemenuhan kebutuhan pakan di Indonesia. Distribusi ke seluruh bagian pulau Jawa dan sekitarnya yang terus kami perluas demi menjangkau lebih jauh.",
                'image' => asset('images/saap/capacity.png')
            ],
            [
                'title' => "Kemitraan",
                'content'   => "PT. Sidoagung Farm menyediakan program kemitraan dan pendampingan bagi para peternak. Sebagai upaya kami untuk keberhasilan bersama peternak akan menerima kontrol dan pendampingan dari Petugas Penyuluh Lapangan, selanjutnya juga seminar terkait kesehatan hewan ternak dan manajemen kandang dari dokter hewan.",
                'image' => asset('images/saap/partnership.png')
            ],
            [
                'title' => "Kualitas",
                'content'   => "Standar kualitas yang PT. Sidoagung Farm terapkan pada tiap produk, membuat kami percaya akan manfaat dari produk-produk kami. Kualitas stabil dan produk yang konsisten.",
                'image' => asset('images/saap/quality.png')
            ],
        ];
        
    @endphp
    <div id="carousel" class="carousel slide mt-5 mb-5" data-ride="carousel" data-interval="3000">
        <div class="carousel-inner" role="listbox">
            @foreach ($slides as $s)
                
                <div class="carousel-item {{ $loop->index == 0 ? 'active' : '' }}">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="row mt-5">
                                <div class="col-12">
                                    <div class="text-right" style="padding:10px; background: linear-gradient(90deg, rgba(255,255,255,1) 0%, rgba(0,166,81,1) 55%);">
                                        <span class="h1-font-size-48 font-weight-bold text-white">{{$s['title']}}</span>
                                    </div>
                                </div>
                            </div>                
                            <div class="mt-3 row justify-content-end">
                                <div class="col-10 ">
                                    <div class="text-justify p-4" style="box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;">
                                        <span class="">{{$s['content']}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-end">
                                <div class="col-9" style="height: 25px; background:#F80202;">
                                    <span>&nbsp;</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <img class="img-talkus img-service-slide" src="{{ $s['image'] }}" style="border-radius:10px;" alt="{{$s['title']}}" title="{{$s['title']}}"/>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <section id="productlist" class="space-ptb " style="background-color: #00A651;">
        <div class="container">
            <div class="row justify-content-center">
                <span class="h3 text-white" style="border-bottom: 4px solid #ED1D24">Produk Kami</span>
            </div>
            <div id="productsect" class="row bg-transparent">
                <div class="col-md-12">
                    <div class="owl-carousel text-left" data-nav-arrow="true" data-nav-dots="true" data-items="1"
                        data-md-items="1" data-sm-items="1" data-xs-items="1" data-xx-items="1" style="z-index: unset;">
                        @foreach ($products as $list)
                            <div class="items">
                                <section class=" ">
                                    <div class="container">
                                        <div class="row justify-content-center">
                                            @foreach ($list as $p)
                                                <div onclick="openForm('{{ encrypt($p->id) }}')" class="col-lg-3 col-md-6 text-center mobile-product mb-4">
                                                    <div class="p-2 d-inline-block border-radius bg-product p-3 " style="height: 320px;">
                                                        {{-- <span class="product-text">{{ $categories[$p->category] ?? ""}}</span>
                                                        <br/> --}}
                                                        <div class="product-imagego mt-2" style="background: url('{{ route('main.getResource', ['id' => $p->mediaId]) }}');"></div>
                                                        <br>
                                                        <a href="javascript:void(0);"
                                                            onclick="openForm('{{ encrypt($p->id) }}')"
                                                            class="mb-4 mt-3 product-text">{{ $p->title }}</a>
                                                        <br>
                                                        <small>{{ $p->description }}</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </section>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="header-inner header-inner-menu h-700 "
        style="background-image: url('{{asset('images/saf/bg-office.jpeg')}}');">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8 header-innermenu-height"></div>
            </div>
        </div>
    </section>

    @include('products.modals')
    @include('products.modal-order')
@endsection

@section('css')
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <link rel="stylesheet" href="{{ asset('js/rolling-slider/rolling.css') }}" />
    
@endsection

@section('script')
    <script src="{{ asset('js/rolling-slider/rolling.js') }}"></script>
    <script>
        var player = new Plyr('video');

        function videoPlay(source, image, mime) {

            player.source = {
                type: 'video',
                title: 'Example title',
                sources: [{
                    src: source,
                    type: mime,
                    size: 720,
                }, ],
                poster: image,
            };
            $('#modalVideo').modal('show');
        }

        $("#modalVideo").on('hide.bs.modal', function() {
            player.stop();
        });

        $(document).ready(function(){
            let header = $(".header")
            header.css("position", "fixed")
                    .css("width", "100%")
                    .css("background", "transparent")
                    .css("box-shadow", "unset")
            header.find(".navbar").first().removeClass("bg-white");

            $(".header-inner").eq(0).css("height", "auto")
        })

        $(window).scroll(function(){
            let header = $(document).find(".header").first()
            if(header.hasClass("sticky-top"))
            {
                header.css("background", "white")
                .css("box-shadow", "0 1px 14px rgb(0 0 0 / 5%)")
                .css("-webkit-box-shadow", "0 1px 14px rgb(0 0 0 / 5%)")
            }else{
                header.css("background", "transparent")
                .css("box-shadow", "unset")
            }
        })
    </script>
    <script>
        $(window).resize(function(){
            let width = $( window ).width();
            if(width > 768)
            {
                $(".img-service-slide").css("display", "block")
            }else{
                $(".img-service-slide").css("display", "none")
            }
        });
    </script>
@endsection
