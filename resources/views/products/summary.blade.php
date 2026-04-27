@extends('shared.master')
@section('meta_title', 'Produk Pakan | PT. Sidoagung Farm')
@section('canonical_url', route('products'))

@section('content')
    @php
    $categories = [
        '' => 'Semua Kategori',
        'karkas' => 'Karkas',
        'boneless' => 'Boneless',
        'trimming' => 'Trimming',
        'sampingan' => 'Sampingan',
    ];
    @endphp

    <x-banner-summary mode="product"></x-banner-summary>

    <section class="space-ptb background-sidoagung">
        <div class="container">
            <div class="row justify-content-center ">
                <div class="col-lg-12 pb-lg-0">
                    <div class="section-title mb-3 pt-4">
                        <h2 class="text-white"> Produk Perusahaan</h2>
                    </div>
                    <p class="text-white">PT. Sidoagung Farm menyediakan Produk Pakan Ternak Berkualitas Tinggi. Produk unggulan kami, yaitu Sido Agung Feed yang telah menjadi pilihan utama bagi peternak unggas di tanah air karena dinilai sangat cocok dengan pola budidaya dan iklim di Indonesia.</p>
                </div>

            </div>
        </div>
    </section>

    <section id="productlist" class="space-ptb bg-light">
        <div class="container">
            <div class="row ">
                <div class="col-lg-2">
                    <h2 class="pt-3 mobile-text-event">Filter</h2>
                </div>
                <div class="col-lg-5 mr-3" id="box_year">
                    {{-- <a class="btn btn-white  mobile-button-event dropdown-toggle mt-3 mb-3 mr-3 w-100" href="#!"
                        id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">{{ $categories[$category] }}</a>
                    <ul id="ul_year" class="dropdown-menu down-menu-dropdown" aria-labelledby="navbarDropdownMenuLink"
                        x-placement="bottom-start"
                        style="position: absolute; cursor: pointer; will-change: transform; top: 0px; left: 0px; transform: translate3d(15px, 65px, 0px);">
                        @foreach ($categories as $key => $val)
                            @php
                                $selected = $key == $category ? 'active' : '';
                            @endphp
                            <li class="dropdown-item-menu-down {{ $selected }}">
                                <a onclick="selectedCategory('{{ $key }}')">{{ $val }}</a>
                            </li>
                        @endforeach
                    </ul> --}}
                </div>
                <div class="col-lg-4 ml-5">
                    <form class="mt-3 mb-3 ml-3" method="get" id="formSearch">
                        {{-- <input type="hidden" name="category" id="category" value=""> --}}
                        <input type="text" class="not-click form-control-ntc form-control" name="keyword"
                            placeholder="Cari.." value="{{ $search }}">
                        <button class="button-search" type="submit" onclick="search()"> <i
                                class="fa fa-search not-click"></i></button>
                    </form>
                </div>
                {{--
                <div class="col-lg-2">
                    <a href="{{ asset('docs/sidofoods_katalog_juni_2022.pdf') }}"
                        class="h6 text-white btn btn-block btn-success mt-3" download style="z-index:unset;">Katalog &nbsp;
                        <i class="fa fa-download"></i></a>
                </div> --}}
            </div>


            <div id="productsect" class="row bg-white surface-contrast surface-medium">
                <div class="col-md-12">
                    <div class="owl-carousel text-left" data-nav-arrow="true" data-nav-dots="true" data-items="1"
                        data-md-items="1" data-sm-items="1" data-xs-items="1" data-xx-items="1" style="z-index: unset;">
                        @foreach ($list as $products)
                            <div class="items">
                                <section class=" bg-white">
                                    <div class="container p-1">
                                        <div class="row justify-content-center">
                                            @foreach ($products as $p)
                                                <div onclick="openForm('{{ encrypt($p->id) }}')"
                                                    class="col-lg-3 col-md-6 text-center mobile-product mb-4 m-2 border"
                                                    style="border-radius: 20px;"
                                                >
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
            @php
                $productMoreUrl = app()->environment('local') ? route('products') : 'https://www.product.sidoagungfarm.com/';
                $productMoreExternal = ! app()->environment('local');
            @endphp
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end align-items-center mt-3">
                    <a
                        href="{{ $productMoreUrl }}"
                        class="btn btn-primary mr-2"
                        @if ($productMoreExternal) target="_blank" rel="noopener noreferrer" @endif
                    >Selengkapnya</a>
                    <span class="feed-tooltip">
                        <i class="fas fa-question-circle feed-tooltip-icon" aria-hidden="true"></i>
                        <span class="feed-tooltiptext" style="width: 150px;">Untuk keterangan produk pakan PT. Sidoagung Farm selengkapnya klik disini </span>
                    </span>
                </div>
            </div>
        </div>
    </section>

    @include('products.modals')
    @include('products.modal-order')
@endsection

@section('css')
    <style>
        .feed-tooltip {
            position: relative;
            display: inline-block;
            opacity: unset;
        }

        .feed-tooltip-icon {
            color: #0b9444;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
        }

        .feed-tooltip .feed-tooltiptext {
            visibility: hidden;
            background-color: rgba(0, 0, 0, 0.85);
            color: #fff;
            text-align: left;
            border-radius: 4px;
            padding: 8px 10px;
            position: absolute;
            z-index: 10;
            bottom: 125%;
            left: 50%;
            margin-left: -75px;
            font-style: normal;
            line-height: 1.3;
        }

        .feed-tooltip:hover .feed-tooltiptext {
            visibility: visible;
        }
    </style>
@endsection

@section('script')
    @parent
    <script>
        function selectedCategory(e) {
            //$("#category").val(e)
            $("#formSearch").submit()
        }
    </script>
@endsection
