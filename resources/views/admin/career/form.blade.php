@extends('admin.master')
@section('page')
    Form Lowongan Kerja
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <form id="frmCareer" class="form-horizontal">
                {{ csrf_field() }}
                <div class="form-group" style="margin-top:15px;">
                    <label for="formPosition" class="col-lg-2 control-label">Posisi <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control input-sm" id="formPosition" placeholder="Posisi" maxlength="100" value="{{old("formPosition") ?? $rs->position}}">
                    </div>
                </div>
                <div class="form-group" >
                    <label for="formLocation" class="col-lg-2 control-label">Lokasi <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control input-sm" id="formLocation" placeholder="Lokasi" maxlength="100" value="{{old("formLocation") ?? $rs->location}}">
                    </div>
                </div>
                <div class="form-group" >
                    <label for="formPostedOn" class="col-lg-2 control-label">Posting Date <span class="text-danger">*</span></label>
                    <div class="col-lg-3">
                        <div class="input-group">
                            <input type="text" class="form-control input-sm" id="formPostedOn" value="{{old("formPostedOn") ?? $rs->postedon}}">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group" >
                    <label for="formClosingDate" class="col-lg-2 control-label">Closing Date <span class="text-danger">*</span></label>
                    <div class="col-lg-3">
                        <div class="input-group">
                            <input type="text" class="form-control input-sm" id="formClosingDate" value="{{old("formClosingDate") ?? $rs->closingdate}}">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group" >
                    <label for="formDescription" class="col-lg-2 control-label">Deskripsi</label>
                    <div class="col-lg-10">
                        <textarea class="form-control input-sm" id="formDescription" rows="10" cols="80">{{old("formDescription") ?? $rs->description}}</textarea>
                    </div>
                </div>
                <div class="form-group" >
                    <label for="formQualification" class="col-lg-2 control-label">Qualification</label>
                    <div class="col-lg-10">
                        <textarea class="form-control input-sm" id="formQualification" rows="10" cols="80">{{ old("formQualification") ?? $rs->qualification}}</textarea>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-lg-6">
                        @if(old("formPublish") ?? $rs->publish)
                            <input type="checkbox" id="formPublish" checked>
                        @else
                            <input type="checkbox" id="formPublish">
                        @endif
                        <label for="formPublish"> Publish </label>
                    </div>
                    <div class="col-lg-6 text-right">
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
            $("#formPostedOn, #formClosingDate").datepicker({
                autoclose: true,
                format: "yyyy-mm-dd"
            })

            $("#formPublish").iCheck({
                checkboxClass: 'icheckbox_square-blue'
            })

            CKEDITOR.replace('formDescription');
            CKEDITOR.replace('formQualification');

            $("#btnBack").click(function(){
                window.location.href = "{{url('/wongelek/karir')}}"
            })

            $("#btnClear").click(function(){
                window.location.href = "{{url('/wongelek/karir/add')}}"
            })

            $("#btnSave").click(function(){
                submitForm();
            });
        })

        function submitForm(){
            let mandatories = ["formPosition", "formLocation", "formPostedOn", "formClosingDate"];
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

            let inputs = mandatories.concat(["formDescription", "formQualification", "formPublish"]);
            inputs.map(function(e){
                $("#" + e).prop("name", e)
            })
            $("#frmCareer")
                .prop("method", "post")
                .prop("action", "{{url('/wongelek/karir/save')}}")
               .submit()
        }
    </script>
@endsection