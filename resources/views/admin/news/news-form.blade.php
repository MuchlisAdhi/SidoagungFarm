@extends('admin.master')
@section('page')
    Form Berita
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <form id="frmNews" class="form-horizontal" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group" style="margin-top:15px;">
                    <label for="formTitle" class="col-lg-2 control-label">Title <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control input-sm" id="formTitle" placeholder="Title" maxlength="100" value="{{old("formTitle") ?? $rs->title}}">
                    </div>
                </div>
                <div class="form-group" style="margin-top:15px;">
                    <label for="formAuthor" class="col-lg-2 control-label">Author <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control input-sm" id="formAuthor" placeholder="Author" maxlength="100" value="{{old("formAuthor") ?? $rs->author}}">
                    </div>
                </div>
                <div class="form-group" >
                    <label for="formPostedOn" class="col-lg-2 control-label">Posting Date <span class="text-danger">*</span></label>
                    <div class="col-lg-3">
                        <div class="input-group">
                            <input type="text" class="form-control input-sm" id="formPostedOn" value="{{old("formPostedOn") ?? $rs->releasedate}}">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group" >
                    <label for="formPostedOn" class="col-lg-2 control-label">Thumbnail <span class="text-danger">*</span></label>
                    <div class="col-lg-3">
                        <input type="file" id="formThumbnail">
                    </div>
                </div>
                <div class="form-group" >
                    <label for="formContent" class="col-lg-2 control-label">Content</label>
                    <div class="col-lg-10">
                        <textarea class="form-control input-sm" id="formContent" rows="10" cols="80">{{old("formContent") ?? $rs->content}}</textarea>
                    </div>
                </div>
                <div class="form-group" >
                    <label for="formClosingDate" class="col-lg-2 control-label">&nbsp; </label>
                    <div class="col-lg-3">
                        @if(old("formPublish") ?? $rs->publish)
                            <input type="checkbox" id="formPublish" checked>
                        @else
                            <input type="checkbox" id="formPublish">
                        @endif
                        <label for="formPublish">&nbsp;Publish </label>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-lg-12 text-right">
                        <button id="btnBack" type="button" class="btn btn-sm btn-default">Kembali</button>
                        <button id="btnClear" type="button" class="btn btn-sm btn-default">Clear</button>
                        <button id="btnSave" type="button" class="btn btn-sm btn-success">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('plugins/datepicker/datepicker3.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/iCheck/square/blue.css') }}">
@endsection

@section('script')
    <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('plugins/iCheck/icheck.min.js') }}"></script>
    <script>
        $(function(){
            $("#formPostedOn").datepicker({
                autoclose: true,
                format: "yyyy-mm-dd"
            })

            $("#formPublish").iCheck({
                checkboxClass: 'icheckbox_square-blue'
            })

            CKEDITOR.replace('formContent');

            $("#btnBack").click(function(){
                window.location.href = "{{url('/wongelek/news')}}"
            })

            $("#btnClear").click(function(){
                window.location.href = "{{url('/wongelek/news/add')}}"
            })

            $("#btnSave").click(function(){
                submitForm();
            });
        })

        function submitForm(){
            const tmb = $("#formThumbnail").prop('files');
            const allowExt = ["image/png", "image/jpg", "image/jpeg"];

            let mandatories = ["formTitle", "formPostedOn", "formAuthor"];
            for(let i in mandatories)
                if($("#" + mandatories).val() == "")
                {
                    $.toast({
                        heading: 'Error',
                        text: `Require's empty`,
                        showHideTransition: 'fade',
                        position: 'bottom-right',
                        icon: 'error'
                    })
                    return;
                }
            
            if (tmb.length < 1) {
                $.toast({
                    heading: 'Error',
                    text: `Require's empty`,
                    showHideTransition: 'fade',
                    position: 'bottom-right',
                    icon: 'error'
                })
                return;
            }

            const file = tmb[0];

            if (!allowExt.includes(file.type)) {
                $.toast({
                    heading: 'Error',
                    text: `Extention file not allowed. (png, jpg, jpeg)`,
                    showHideTransition: 'fade',
                    position: 'bottom-right',
                    icon: 'error'
                })
                return;
            }

            let inputs = mandatories.concat(["formContent", "formPublish", "formThumbnail"]);
            inputs.map(function(e){
                $("#" + e).prop("name", e)
            })

            if (!AdminSubmit.start("#btnSave", "Menyimpan...")) {
                return;
            }

            $("#frmNews")
                .prop("method", "post")
                .prop("action", "{{url('/wongelek/news/save')}}")
               .submit()
        }
    </script>
@endsection
