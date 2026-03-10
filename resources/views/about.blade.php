@extends('shared.master')

@section('content')
    <x-banner-summary mode="about"></x-banner-summary>

    <section class="pt-4 pb-3"
        style="background-image: url('{{ asset('images/saap/green-radius-right.png') }}'); background-size:100% 90px; background-repeat: no-repeat;"
        id="about-us">
        <div class="container ">
            <div class="row">
                <span class="h4 text-white">Sekilas Sido PT. Sidoagung Farm</span>
            </div>
        </div>
    </section>

    <section class="pb-2">
        <div class="container ">
            <div class="row mt-5">
                <div class="col-lg-6">
                    <p class="mb-md-4 mb-2 text-justify">
                        Sidoagung Farm adalah bagian dari perusahaan Sido Agung Group yang bergerak di bidang 
                        peternakan unggas terintegrasi. Usaha peternakan Sido Agung Group dimulai pada tahun 1982 dan terus 
                        berkembang hingga saat ini.
                        <br /><br />
                        Seiring dengan usaha peternakan yang terus berkembang, pada tahun 2018 Sido Agung Group melakukan 
                        pengembangan usaha dengan mendirikan Sidoagung Farm di Magelang yang bergerak dalam bidang 
                        produksi pakan ternak.
                        <br /><br />
                        Dengan kapasitas produksi sebesar 25.000 ton/bulan, Sidoagung Farm berkomitmen untuk menjaga 
                        dan terus meningkatkan kualitas produk. Program kemitraan dan budidaya juga menjadi salah satu 
                        komitmen dalam hal kebermanfaatan bagi masyarakat khususnya para peternak.
                    </p>
                </div>
                <div class="col-lg-1">
                    &nbsp;
                </div>
                <div class="col-lg-5">
                    <img class="img-gms mt-xl-n4" src="{{ asset('images/saf/bg-office.jpeg') }}"
                        alt="Sidoagung Farm" style="">
                </div>
            </div>
        </div>
    </section>

    <section class="pt-4 pb-3"
        style="background-image: url('{{ asset('images/saf/green-radius-left.png') }}'); background-size:100% 75px; background-repeat: no-repeat;"
        id="manajemen">
        <div class="container ">
            <div class="row">
                <div class="col-12 text-right">
                    <span class="h4 text-white">Manajemen</span>
                </div>
            </div>
        </div>
    </section>

    <section class="space-ptb">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-lg-3 mb-4 mb-lg-0 pt-10">
                    <img class="img-fluid" src="{{ asset('images/saap/bram-sebastian.png') }}"
                        alt="sidoagung-bram-sebastian">
                </div>
                <div class="col-lg-9 mb-4 mb-lg-0">
                    <div class="col-md-12 bg-white border-radius mt-3">
                        <div class="pl-5 pb-5 pt-5 ">
                            <h5 class="text-primary mb-2">Bram Sebastian</h5>
                            <p style="font-style: italic; margin-top: -10px;">Direktur Utama</p>
                            <p class="mb-2 text-justify">
                                Warga Negara Indonesia, lahir di Magelang pada tanggal 19 November 1982. Menjabat sebagai
                                Direktur Utama Sido Agung Group sejak 2021.
                                <br /><br />
                                Sebelumnya Beliau berkarir sebagai sebagai Partnerships Development Manager Sido Agung Group
                                (2011-2013), kemudian sebagai Project Head Sido Agung Group (2013-2015), Saat ini beliau
                                juga memegang berbagai jabatan managerial lain di unit-unit usaha Sido Agung Group seperti
                                Direktur Utama PT Sido Agung Agro Prima (2015-sekarang), PT Sido Sari Multifarm dan PT
                                Sidoagung Foods Processing (2018-sekarang).
                                <br /><br />
                                Meraih gelar Bachelor of Science - BS, Electrical Engineer dari Iowa State University
                                (2000-2004) dan Master of Science - MS, Poultry Science dari The University of Georgia
                                (2005-2007) serta Doctor of Philosophy - PhD, BioInformatics dari The University of Georgia
                                (2007-2010).

                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-3 mb-4 mb-lg-0 pt-10">
                    <img class="img-fluid" src="{{ asset('images/saap/asrokh-nawawi.png') }}" alt="sidoagung-asrokh-nawawi">
                </div>
                <div class="col-lg-9 mb-4 mb-lg-0">
                    <div class="col-md-12 bg-white border-radius mt-3">
                        <div class="pl-5 pb-5 pt-5 ">
                            <h5 class="text-primary mb-2">Asrokh Nawawi</h5>
                            <p style="font-style: italic; margin-top: -10px;">Direktur Marketing</p>
                            <p class="mb-2 text-justify">
                                Warga Negara Indonesia, lahir pada tanggal 14 Juli 1964, berdomisili di Jakarta. Menjabat
                                sebagai Direktur Marketing Sido Agung Group. Beliau bertanggung jawab pada operasi pemasaran
                                secara keseluruhan perusahaan seperti merencanakan, mengarahkan dan mengawasi seluruh
                                kegiatan pemasaran perusahaan.
                                <br/><br/>
                                Asrokh Nawawi berpengalaman di dunia Marketing selama lebih dari 20 tahun dan bertanggung
                                jawab pada kendali pemasaran di beberapa regional seperti Jawa Timur, Jawa Barat, Jawa
                                Tengah, dan Sulawesi Selatan.
                                <br/><br/>
                                Sebelum bergabung dengan Sido Agung, beliau memiliki pengalaman di PT Japfa Comfeed
                                Indonesia dengan jabatan terakhir sebagai Marketing Manager sejak tahun 1991. Beliau meraih
                                gelar Sarjana Kedokteran Hewan dari Universitas Gadjah Mada (2001).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="pt-4 pb-3"
        style="background-image: url('{{ asset('images/saap/green-radius-right.png') }}'); background-size:100% 90px; background-repeat: no-repeat;"
        id="visimisi">
        <div class="container ">
            <div class="row">
                <span class="h4 text-white">Visi & Misi</span>
            </div>
        </div>
    </section>

    <section class="space-ptb">
        <div class="container">
            <div class="row justify-content-md-center mt-4">
                <div class="col-lg-5 text-center">
                    <span class="text-primary font-weight-bold font-xll">Visi</span>
                    <ul style="margin-left: -25px;">
                        <li class="text-primary text-justify">
                            Menjadi Perusahaan Pakan Ternak Terkemuka Di Indonesia.
                        </li>
                        <li class="text-primary text-justify">
                            Menjadi Mitra Terpercaya Bagi Industri Peternakan Maupun Peternak-peternak Rakyat.
                        </li>
                    </ul>
                </div>
                <div class="col-lg-5 text-center">
                    <span class="text-primary font-weight-bold font-xll">Misi</span>
                    <ul style="margin-left: -25px;">
                        <li class="text-primary text-justify">
                            Memproduksi Pakan Ternak Berkualitas Dengan Harga Yang Ekonomis.
                        </li>
                        <li class="text-primary text-justify">
                            Mengembangkan Sektor Ekonomi Pedesaan Dengan Mendorong Petani-petani Jagung.
                        </li>
                        <li class="text-primary text-justify">
                            Mengembangkan Sektor Ekonomi Pedesaan Melalui Kemitraan Dengan Peternak-peternak Kecil.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- <section class="pt-4 pb-3" style="background-color: #00A651;" id="about-us">
        <div class="container ">
            <div class="row justify-content-center">
                <span class="h4 text-white">Sekilas Tentang Kami</span>
            </div>
        </div>
    </section>
    <section class="pb-2">
        <div class="container ">
            <div class="text-left rounded justify-content-center py-5 px-3  ">
                <p class="mb-md-4 mb-2 text-justify">
                    Rumah Potong Hewan Unggas (RPHU) PT Sidoagung Foods Processing mulai aktif beroperasi sejak tahun 2018
                    dan berlokasi di Desa Sukasenang, Kecamatan Banyuresmi, Kabupaten Garut. Dengan kapasitas potong, 2.000
                    ekor Ayam/jam RPHU ini mempekerjakan lebih dari 120 orang untuk mendukung aktivitas operasionalnya.
                    <br/><br/>
                    RPHU PT Sidoagung Foods Processing kini beragam produk ayam potong beku (karkas) dan produk turunannya,
                    mulai dari ayam potong utuh beragam ukuran daging ayam tanpa tulang, kepala dan ceker ayam, kulit,
                    jeroan dan produk lain untuk kebutuhan industri. Seluruh proses produksi di RPHU ini dijalankan dengan
                    mesin-mesin terbaik serta tenaga Profesional.
                    <br/><br/>
                    RPHU PT Sidoagung Foods Processing juga telah mengantongi sertifikat NKV-1 dan Halal. Hal ini menegaskan
                    bahwa RPHU ini sudah memenuhi standar yang baik dalam produksi. Dengan Area pemasaran hampir di seluruh
                    Jawa penjualan RPHU PT Sidoagung Foods Processing terus tumbuh setiap bulannya. Sepanjang tahun 2022
                    sendiri Penjualan RPHU ini rata tumbuh 5-10% setiap bulannya.
                    <br/><br/>
                    Sebagai bagian dari usaha menjaga keberlanjutan usahanya RPHU PT Sidoagung Foods Processing secara aktif
                    terlibat dalam setiap usaha pengembangan masyarakat, melalui program tanggap bencana, pengembangan
                    sosial dan kewirausahaan serta pendidikan anak. Dengan mengedepankan sinergi yang baik bersama
                    masyarakat, terutama masyarakat sekitar lokasi usaha, RPHU PT Sidoagung Foods Processing berharap dapat
                    terus berkembang sebagai usaha dan memberikan dampak positif yang seluas-luasnya.
                </p>
            </div>
        </div>
    </section>

    <section class="pt-4 pb-3" style="background-color: #00A651;" id="news-and-csr">
        <div class="container ">
            <div class="row justify-content-center">
                <span class="h4 text-white">Corporate Social Responsibility dan Berita</span>
            </div>
        </div>
    </section>
    <section class="pt-5 pb-4">
        <div class="container ">
            @php
                $list = $news->first();
                $row = 0;
            @endphp
            <div class="row">
                @if ($list)
                    <div class="col-lg-6">
                        <img class="img-fluid" src="{{ route('main.getResource', ['id' => $list->thumbnail]) }}"
                            alt="">
                        <div class="mt-2">
                            <span class="h6">Berita Terbaru</span>
                        </div>
                        <div class="mt-1">
                            <span class="h4">{{ $list->title }}</span>
                        </div>
                    </div>
                @endif
                <div class="col-lg-6">
                    @foreach ($news as $r)
                        @if ($loop->index > 0)
                            @php
                                $cls = $loop->index > 1 ? 'row mt-180-article' : 'row';
                            @endphp
                            <div class="{{ $cls }}">
                                <div class="col-lg-5">
                                    <img class="img-fluid" src="{{ route('main.getResource', ['id' => $r->thumbnail]) }}"
                                        alt="">
                                </div>
                                <div class="col-lg-7">
                                    <div class="mt-2">
                                        <span class="h5">{{ $r->title }}</span>
                                    </div>
                                    <div class="mt-1 text-justify">
                                        <span class="h7">{!! Str::limit($r->content, 150, '...') !!}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                </div>
            </div>
        </div>
    </section> --}}
@endsection
