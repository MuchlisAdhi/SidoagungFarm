@if ($banner)
    <section class="header-inner header-inner-menu h-500 "
        style="background-image: url('{{ route('main.getResource', ['id' => $banner->mediaId]) }}');">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8 header-innermenu-height"></div>
            </div>
        </div>
    </section>
@endif