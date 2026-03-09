@extends('shared.master')

@section('content')
<x-banner-summary mode="news"></x-banner-summary>

<section>
    {{-- NEW  --}}
    <section class="space-ptb ">
        <div class="container">
            <div class="row justify-content-center background-sidoagung" style="border-radius: 25px;">
                <div class="col-lg-12 pb-lg-0">
                    <div class="section-title mb-3 pt-3 text-center">
                        <h2 class="text-white">Berita & CSR Seputar Sido Agung Agro Prima</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="container-list">
        <section id="listBlock"></section>
    </section>
    <section id="container-detail"></section>

    {{-- CSR --}}
    {{-- <section class="space-ptb ">
        <div class="container">
            <div class="row justify-content-center background-sidoagung" style="border-radius: 25px;">
                <div class="col-lg-12 pb-lg-0">
                    <div class="section-title mb-3 pt-3 text-center">
                        <h2 class="text-white">Corporate Social Responsibility</h2>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
</section>

{{-- <section id="container-detail"></section> --}}

{{-- <section class="bg-light">
    <div class="container mobile-desk-container-event">
        <div id="blog-item">
            <div class="row ">
                <div class="col-lg-5">
                    <h2 class="pt-3 mobile-text-event">&nbsp;</h2>
                </div>
                <div class="col-lg-2 mr-3" id="box_year">
                </div>
                <div class="col-lg-4 ml-5">
                    <form class="mt-3 mb-3 ml-3" method="get">
                        <input type="hidden" name="year" value="">
                        <input type="text" class="not-click form-control-ntc form-control" name="keyword"
                            placeholder="Search.." value="">
                        <button class="button-search" type="submit" onclick="search()"> <i
                                class="fa fa-search not-click"></i></button>
                    </form>
                </div>
            </div>
            <div class="row" id="listBlock"></div>

            <div class="row" id="detailBlock"></div>
        </div>
    </div>
</section> --}}
@endsection

@section('script')
<script>
    
    var keyword = "";
    var page = 1;
    $(function(){

        getList();
        
        $("#formKeyword").on("change", function(){
            keyword = $(this).val();
            page = 1;
            getList();
        })

        $("#btnSearch").on("click", function(){
            keyword = $("#formKeyword").val();
            page = 1;
            getList();
        });

        $('#formKeyword').keypress(function (e) {
            if (e.which == 13) {
                keyword = $("#formKeyword").val();
                page = 1;
                getList();
                return;
            }
        });
    });

    function getList(){
        $.get("{{route('csr.getList')}}?mode=news&keyword=" + keyword + "&page=" + page)
        .done(function(r){
            $(document).find("#listBlock").eq(0).html(r)
        })
        .fail(function(e){
            console.log(e)
        })
    }

    function pageClicked(p)
    {
        keyword = $("#formKeyword").val();
        page = p;
        getList();
        return;
    }

    function showDetail(slug, mode)
    {
        console.log(mode)
        let listBlock = $(document).find("#container-list").eq(0);
        let detailBlock = $(document).find("#container-detail").eq(0);

        $.get(`{{route('csr.getDetail')}}?mode=${mode}&slug=` + slug)
            .done(function(r){
                detailBlock.html(r);

                listBlock.hide();
                detailBlock.show();
            })
            .fail(function(e){
                console.log(e)
            })
    }

    function hideDetail()
    {
        let listBlock = $(document).find("#container-list").eq(0);
        let detailBlock = $(document).find("#container-detail").eq(0);

        listBlock.show();
        detailBlock.hide();
    }
    
</script>
@endsection