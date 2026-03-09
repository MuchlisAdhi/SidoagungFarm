<div class="container bg-white gms-container our-container ">
    <div class="container bg-white pb-3">
        <div class="navigation post-navigation my-md-4 ">
            <div>
                <a class="btn btn-circle btn-warning text-white " href="#!" onclick="hideDetail()"><i id="arrow-icon"
                        class="fas fa-arrow-left"></i> </a> <a style="position: relative;" id="arrow-text-backto"
                    class="text-warning "> Kembali</a>
            </div>
        </div>
        <hr>
        
        <div class="image-postingan"
            style="background: url('{{ route('main.getResource', ['id' => $r->thumbnail]) }}');">
        </div>
        <div class="row">
            <div class="col-12 blog-post-details">
                <h3 class="blog-post-title">
                    {{$r->title}}
                </h3>
                <div style="position:relative; top:-10px;" class="d-sm-flex align-items-center">
                    <div class="blog-post-meta pr-4">
                        <a href="#"><i class="far fa-eye pr-1"></i>{{$r->viewer}}</a>
                    </div>
                    <div class="blog-post-meta pr-4">
                        <a href="#"><i class="far fa-calendar pr-1"></i>{{date("M d, Y", strtotime($r->releasedate))}}</a>
                    </div>
                    @if($r->author ?? null)
                        <div class="blog-post-meta pr-4">
                            <a href="#"><i class="far fa-calendar pr-1"></i>Oleh: {{$r->author}}</a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-12 bg-white overflow-scrool-detail-blog pt-2" id="style-scroll-1">
                {!! $r->content !!}
            </div>
        </div>
    </div>
</div>
