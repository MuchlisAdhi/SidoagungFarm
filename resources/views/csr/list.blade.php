{{-- <div class="container bg-white pt-3">
    <div class="row pb-3">
        @foreach ($list as $l)
            <div class="col-lg-6 card-post col-sm-6 mb-4 pt-4 mb-lg-0">
                <div class="blog-post bg-white event-gms">
                    <div class="blog-post-meta pr-4" style="position: absolute;z-index: 99; right:0px; top:20px;">
                        <a class="text-white"><i class="far fa-eye pr-1"></i>{{ $l->viewer }}</a>
                    </div>
                    <div class="blog-post-meta pr-4" style="position: absolute;z-index: 99; left:40px; top:20px;">
                        <a class="text-white">{{ date('M d, Y', strtotime($l->releasedate)) }}</a>
                    </div>
                    <div style="background:url('{{ route('main.getResource', ['id' => $l->thumbnail]) }}')"
                        class="blog-post-image">
                        <h5 class="blog-post-title csr-title">
                        </h5>
                    </div>
                    <div class="blog-post-content pb-event-gms">
                        <div class="blog-post-details">
                            <a onclick="showDetail('{{ str_replace(' ', '-', strtolower($l->title)) }}')">
                                <h5 class="blog-post-title gms-event-title mb-0 text-white" id="blog_157">
                                    {{ $l->title }}
                                </h5>
                            </a>
                        </div>
                        <div class="blog-post-info pt-3">
                            <div style="position: absolute;z-index: 99; right:40px; bottom:10px;"
                                class="blog-post-author">
                                <a style="height:40px; width:40px; border-radius:99px;"
                                    class="btn btn-warning text-white "
                                    onclick="showDetail('{{ str_replace(' ', '-', strtolower($l->title)) }}')"
                                    ><i
                                        style="position: relative; top:7px;" class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <center>
        <div class="pagination p1">
            <ul>
                @for($i = 1; $i <= $total; $i++)
                    @if($i == $page)
                        <a style="cursor: pointer;" class="is-active">
                            <li>{{$i}}</li>
                        </a>
                    @else
                        <a style="cursor: pointer;" onclick="pageClicked({{$i}})">
                            <li>{{$i}}</li>
                        </a>
                    @endif
                @endfor
            </ul>
        </div>
    </center>
</div> --}}

@foreach ($list as $l)
    @if($loop->index % 2 == 0)
        <div class="container p-5">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-4 mb-lg-0 text-right">
                    <div class="container image-text-right-container">
                        <h3 class="mb-4">{{ $l->title }}</h3>
                        <p class="mb-4">
                            {!! Str::limit($l->content, 100, "...") !!}
                        </p>
                        <a href="#!" onclick="showDetail('{{ str_replace(' ', '-', strtolower($l->title)) }}', '{{ $l->mode }}')" class="btn btn-primary ">Selengkapnya</a>
                    </div>
                </div>
                <div class="col-lg-7 text-left">
                    <img class="img-fluid" style="border-radius: 20px;" src="{{ route('main.getResource', ['id' => $l->thumbnail]) }}" alt="{{ $l->title }}" title="{{ $l->title }}">
                </div>
            </div>
        </div>
    @else
        <div class="container p-5">
            <div class="row align-items-center">
                <div class="col-lg-7 text-right">
                    <img class="img-fluid" style="border-radius: 20px;" src="{{ route('main.getResource', ['id' => $l->thumbnail]) }}" alt="{{ $l->title }}" title="{{ $l->title }}">
                </div>
                <div class="col-lg-5 mb-4 mb-lg-0 text-left">
                    <div class="container image-text-right-container">
                        <h3 class="mb-4">{{ $l->title }}</h3>
                        <p class="mb-4">
                            {!! Str::limit($l->content, 100, "...") !!}
                        </p>
                    <a href="#!" onclick="showDetail('{{ str_replace(' ', '-', strtolower($l->title)) }}', '{{ $l->mode }}')" class="btn btn-primary ">Selengkapnya</a>
                    </div>
                </div>
                
            </div>
        </div>
    @endif
@endforeach
<center>
    <div class="pagination p1">
        <ul>
            @for($i = 1; $i <= $total; $i++)
                @if($i == $page)
                    <a style="cursor: pointer;" class="is-active">
                        <li>{{$i}}</li>
                    </a>
                @else
                    <a style="cursor: pointer;" onclick="pageClicked({{$i}})">
                        <li>{{$i}}</li>
                    </a>
                @endif
            @endfor
        </ul>
    </div>
</center>