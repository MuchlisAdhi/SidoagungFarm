@extends('admin.master')
@section('page')
    Testimoni
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-lg-12" style="margin-bottom: 5px;">
                    <button class="btn btn-sm btn-primary pull-right" id="btnAddTestimoni">Add Testimoni</button>
                </div>
            </div>
            <table id="tblTestimoni" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 20%;">Photo</th>
                        <th style="width: 15%;">Name</th>
                        <th style="width: 15%;">Title</th>
                        <th>Testimoni</th>
                        <th style="width: 5%;">Action</th>
                    </tr>
                </thead>
                <tbody id="tblBodyTestimoni">
                    @foreach ($list as $u)
                        <tr>
                            <td></td>
                            <td>
                                <div class="attachment-block clearfix" style="text-align: center;">
                                    <img class="img-responsive" src="{{ route('main.getResource', ['id' => $u->photo]) }}"
                                        alt="attachment image">
                                </div>
                            </td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->title }}</td>
                            <td>{{ $u->testimoni }}</td>
                            
                            <td style="text-align: center;">
                                <a href="javascript:void(0);" class="btnRemove text-danger" title="Remove"
                                    onclick="removeSelected('{{ encrypt($u->id) }}')">
                                    <i class="fa fa-trash" style="font-size: 20px;"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="modalRemoveConfirm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Remove Confirm</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure to remove Testimoni ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnRemoveYes">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modalFormTestimoni">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Form Testimoni</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="formName" class="col-sm-3 control-label">Name <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="formName" placeholder="Name"
                                        maxlength="30">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="formTitle" class="col-sm-3 control-label">Title <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="formTitle" placeholder="Title"
                                        maxlength="30">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="formImage" class="col-sm-3 control-label">Photo <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="file" id="formImage">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="formTestimoni" class="col-sm-3 control-label">Testimoni <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" id="formTestimoni"></textarea>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnSaveForm">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css') }}">
@endsection

@section('script')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script>
        var bannerSelected = "";
        $(document).ready(function() {
            $("#tblBodyTestimoni").find("tr").each(function(i, e) {
                $(this).find("td").eq(0).html(i + 1)
            });
            $("#tblTestimoni").DataTable();

            $("#btnRemoveYes").click(function() {
                $.get("{{ url('/admin/testimoni/remove') }}/" + bannerSelected, function() {
                    $("#modalRemoveConfirm").modal("hide");
                    window.location.reload();
                })
                $("#modalRemoveConfirm").modal("hide")
            })

            $("#btnAddTestimoni").click(function() {
                clearForm();
                $("#modalFormTestimoni").modal("show");
            });

            $("#btnSaveForm").click(function() {
                const id = bannerSelected;
                const name = $("#formName").val();
                const title = $("#formTitle").val();
                const testimoni = $("#formTestimoni").val();
                const images = $("#formImage").prop('files');

                const allowExt = ["image/png", "image/jpg", "image/jpeg"];

                if (images.length < 1 || name == "" || testimoni == "") {
                    $.toast({
                        heading: 'Error',
                        text: `Require's empty`,
                        showHideTransition: 'fade',
                        position: 'bottom-right',
                        icon: 'error'
                    })
                    return;
                }

                for (let i = 0; i < images.length; i++) {
                    console.log(images[i].type)
                    if (!allowExt.includes(images[i].type)) {
                        $.toast({
                            heading: 'Error',
                            text: `Extention file not allowed. (png, jpg, jpeg)`,
                            showHideTransition: 'fade',
                            position: 'bottom-right',
                            icon: 'error'
                        })
                        return;
                    }
                }

                if (!AdminSubmit.start("#btnSaveForm", "Menyimpan...")) {
                    return;
                }

                var form = new FormData();
                form.append('image', images[0]);
                form.append('formName', name);
                form.append('formTitle', title);
                form.append('formTestimoni', testimoni);

                $.ajax({
                    url: "{{ url('/admin/testimoni/save') }}",
                    type: "POST",
                    data: form,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(res) {
                        if (res.code == 200) {
                            window.location.reload();
                        } else {
                            AdminSubmit.stop("#btnSaveForm");
                            $.toast({
                                heading: 'Error',
                                text: res.msg,
                                showHideTransition: 'fade',
                                position: 'bottom-right',
                                icon: 'error'
                            })
                        }
                    },
                    error: function(e) {
                        AdminSubmit.stop("#btnSaveForm");
                        $.toast({
                            heading: 'Error',
                            text: "Gagal Menyimpan form.",
                            showHideTransition: 'fade',
                            position: 'bottom-right',
                            icon: 'error'
                        })
                    }
                });

            });
        });

        function removeSelected(id) {
            bannerSelected = id;
            $("#modalRemoveConfirm").modal("show")
        }

        function clearForm() {
            bannerSelected = "";
            $("#formImage, #formName, #formTestimoni").val("");
        }
    </script>
@endsection
