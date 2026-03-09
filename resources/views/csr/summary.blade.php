@extends('shared.master')

@section('content')
<x-banner-summary mode="csr"></x-banner-summary>
<section class="space-ptb background-sidoagung">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-12 mb-4 mb-lg-0">
        <div class="section-title pt-5">
          <h2 class="mb-3 text-white">Corporate Social Responsibility</h2>
          <p class="text-white">
            {!! Str::headline("Sebagai Bagian Dari Usaha Group Untuk Menciptakan Lingkungan Usaha Yang Berkelanjutan, Sido Agung Group secara berkelanjutan Menyelenggarakan Berbagai Program Corporate Social Responsibility.") !!}</p>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="space-pt ">
   <div class="container">
      <div class="row align-items-center">
         <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="container image-text-left-container">
               <h3 class="mb-4">Pendidikan</h3>
               <p class="mb-4">
                  {!! Str::headline("Dalam Hal Pendidikan, Sido Agung Group Berkomitmen Untuk Terus Berperan Aktif Melalui Beragam Program Kerjasama Dengan Banyak Institusi Pendidikan. Di Lain Itu Sido Agung Group Juga Berinisiatif Untuk Ikut Mencetak Tenaga-Tenaga Ahli Peternakan Melaui Pembukaan Fakultas Peternakan Di Universitas Nahdlatul Ulama, Cirebon.") !!}</p>
               <a href="{{route('csr.education')}}" class="btn btn-primary ">Selengkapnya</a>
            </div>
         </div>
         <div class="col-lg-6">
            <img class="img-fluid img-shadow-left" src="{{asset('images/sag/csr/education.jpg')}}" alt="">
         </div>
      </div>
   </div>
</section>

<section class="space-pt  ">
   <div class="container ">
      <div class="row align-items-center">
         <div class="col-lg-6">
            <img class="img-fluid img-shadow-right" src="{{asset('images/sag/csr/safety.jpg')}}" alt="">
         </div>
         <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="container image-text-right-container">
               <h3 class="mb-4">Kesehatan dan Keselamatan Kerja</h3>
               <p class="mb-4">
                  {!! Str::headline("Sido Agung Group Memandang Karyawan Sebagai Aset Inti Dalam Setiap Usahanya. Sido Agung Group Mengedepankan Prinsip Kesetaraan Dalam Kesempatan, Pemeliharan Lingkungan Kerja Yang Sehat Kondusif Dan Kompetitif, Sambil Terus Berkomitmen Dalam Penerapan Keselamatan Kerja Yang Excellence.") !!}</p>
               <a href="{{route('csr.safety')}}" class="btn btn-primary ">Selengkapnya</a>
            </div>
         </div>
      </div>
   </div>
</section>
<section class="space-pt ">
   <div class="container">
      <div class="row align-items-center">
         <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="container image-text-left-container">
               <h3 class="mb-4">Sosial</h3>
               <p class="mb-4">
                  {!! Str::headline('Sido Agung Group Terus Berupaya Meningkatkan Martabat Hidup Masyarakat Di Sekitar Wilayah Kerjanya. Dengan Prinsip "Bisnis Yang Baik dapat Menciptakan Komunitas Yang Baik". Kami Mencoba Terlibat Aktif Dalam Setiap Upaya Pengembangan Masyarakat Atau Komunitas.') !!}</p>
               <a href="{{route('csr.sosial')}}" class="btn btn-primary ">Selengkapnya</a>
            </div>
         </div>
         <div class="col-lg-6">
            <img class="img-fluid img-shadow-left" src="{{asset('images/sag/csr/sosial.jpg')}}" alt="">
         </div>
      </div>
   </div>
</section>
@endsection