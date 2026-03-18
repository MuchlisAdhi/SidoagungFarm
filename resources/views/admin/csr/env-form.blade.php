@extends('admin.master')
@section('page')
    Form Pendidikan
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <form id="frmEnv" class="form-horizontal" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group" style="margin-top:15px;">
                    <label for="formTitle" class="col-lg-2 control-label">Title <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control input-sm" id="formTitle" placeholder="Title" maxlength="100" value="{{old("formTitle") ?? $rs->title}}">
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
                window.location.href = "{{url('/admin/csr/env')}}"
            })

            $("#btnClear").click(function(){
                window.location.href = "{{url('/admin/csr/env/add')}}"
            })

            $("#btnSave").click(function(){
                submitForm();
            });
        })

        function submitForm(){
            const tmb = $("#formThumbnail").prop('files');
            const allowMime = ["image/png", "image/jpg", "image/jpeg"];
            const allowExt = ["png", "jpg", "jpeg"];
            const maxFileSize = 5 * 1024 * 1024;
            const isEdit = @json(!empty($rs->id));

            const requiredFields = [
                { id: "formTitle", label: "Title" },
                { id: "formPostedOn", label: "Posting Date" }
            ];

            for (let i = 0; i < requiredFields.length; i++) {
                const field = requiredFields[i];
                const value = ($("#" + field.id).val() || "").trim();
                if (!value) {
                    showValidationError(field.label + " wajib diisi.");
                    $("#" + field.id).focus();
                    return;
                }
            }

            const postedOn = ($("#formPostedOn").val() || "").trim();
            if (!/^\d{4}-\d{2}-\d{2}$/.test(postedOn)) {
                showValidationError("Format Posting Date harus yyyy-mm-dd.");
                $("#formPostedOn").focus();
                return;
            }
            
            if (!isEdit && tmb.length < 1) {
                showValidationError("Thumbnail wajib diisi.");
                return;
            }

            if (tmb.length > 0) {
                const file = tmb[0];
                const mimeType = (file.type || "").toLowerCase();
                const ext = ((file.name || "").split(".").pop() || "").toLowerCase();

                if (file.size > maxFileSize) {
                    showValidationError("Ukuran thumbnail maksimal 5 MB.");
                    return;
                }

                if (!allowMime.includes(mimeType) && !allowExt.includes(ext)) {
                    showValidationError("Ekstensi thumbnail harus jpg, jpeg, atau png.");
                    return;
                }
            }

            if (CKEDITOR.instances.formContent) {
                CKEDITOR.instances.formContent.updateElement();
            }

            let inputs = requiredFields.map(function(field){ return field.id; }).concat(["formContent", "formPublish", "formThumbnail"]);
            inputs.map(function(e){
                $("#" + e).prop("name", e)
            })

            if (!AdminSubmit.start("#btnSave", "Menyimpan...")) {
                return;
            }

            $("#frmEnv")
                .prop("method", "post")
                .prop("action", "/admin/csr/env/save")
               .submit()
        }

        function showValidationError(message) {
            $.toast({
                heading: 'Error',
                text: message,
                showHideTransition: 'fade',
                position: 'bottom-right',
                icon: 'error'
            });
        }
    </script>
@endsection
