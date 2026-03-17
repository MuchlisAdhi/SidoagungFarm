@extends('admin.master')
@section('page')
    Banners
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-lg-12" style="margin-bottom: 5px;">
                    <button class="btn btn-sm btn-primary pull-right" id="btnAddBanner">Add Banner</button>
                </div>
            </div>
            <table id="tblBanner" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 20%;">Image</th>
                        <th>Title</th>
                        <th style="width: 5%;">Published</th>
                        <th style="width: 5%;">Action</th>
                    </tr>
                </thead>
                <tbody id="tblBodyBanner">
                    @foreach ($banners as $u)
                        <tr>
                            <td></td>
                            <td>
                                <div class="attachment-block clearfix" style="text-align: center;">
                                    <img class="img-responsive" src="{{ route('main.getResource', ['id' => $u->mediaId]) }}"
                                        alt="attachment image">
                                </div>
                            </td>
                            <td>{{ $u->title }}</td>
                            <td style="text-align: center;">
                                @if ($u->publish)
                                    <a href="javascript:void(0)" onclick="unPublish('{{ encrypt($u->id) }}')">
                                        <i class="fa fa-check-circle-o" style="font-size: 20px;"></i>
                                    </a>
                                @else
                                    <a href="javascript:void(0)" onclick="publish('{{ encrypt($u->id) }}')">
                                        <i class="fa fa-circle-o text-danger" style="font-size: 20px;"></i>
                                    </a>
                                @endif
                            </td>
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
                    <p>Are you sure to remove Banner ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnRemoveYes">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modalFormBanner">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Form Banner</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="formTitle" class="col-sm-3 control-label">Title <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="formTitle" placeholder="Title"
                                        maxlength="30">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="formTitle" class="col-sm-3 control-label">Image <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="file" id="formImage">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="formTitle" class="col-sm-3 control-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="formPublish"> Publish
                                        </label>
                                    </div>
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
            $("#tblBodyBanner").find("tr").each(function(i, e) {
                $(this).find("td").eq(0).html(i + 1)
            });
            $("#tblBanner").DataTable();

            $("#btnRemoveYes").click(function() {
                $.get('{{ url('/admin/home/banner/remove') }}/' + bannerSelected, function() {
                    $("#modalRemoveConfirm").modal("hide");
                    window.location.reload();
                })
                $("#modalRemoveConfirm").modal("hide")
            })

            $("#btnAddBanner").click(function() {
                clearForm();
                $("#modalFormBanner").modal("show");
            });

            $("#btnSaveForm").click(function() {
                const id = bannerSelected;
                const title = $("#formTitle").val();
                const images = $("#formImage").prop('files');
                const publish = $("#formPublish").is(":checked") ? 1 : 0;

                const allowExt = ["image/png", "image/jpg", "image/jpeg"];

                if (images.length < 1 || title == "") {
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
                form.append('title', title);
                form.append('publish', publish);

                $.ajax({
                    url: "{{ url('/admin/home/banner/save') }}",
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
            $("#formImage, #formTitle").val("");
            $('#formPublish').prop('checked', false);
        }

        function unPublish(id) {
            $.get("{{ url('/admin/home/banner/publish') }}/" + id + "?publish=0", function() {
                window.location.reload();
            });
        }

        function publish(id) {
            $.get("{{ url('/admin/home/banner/publish') }}/" + id + "?publish=1", function() {
                window.location.reload();
            });
        }
    </script>
@endsection
