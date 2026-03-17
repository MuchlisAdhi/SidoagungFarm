@extends('admin.master')
@section('page')
    Produk
@endsection
@php
    $categories = [
        ''              => "",
        'karkas'        => "Karkas",
        'boneless'     => "Boneless",
        'trimming'     => "Trimming",
        'sampingan'    => "Sampingan"
    ];
@endphp

@section('content')
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-lg-12" style="margin-bottom: 5px;">
                    <button class="btn btn-sm btn-primary pull-right" id="btnAddProduct">Add Product</button>
                </div>
            </div>
            <table id="tblProduct" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 100px;">Image</th>
                        <th>Title</th>
                        {{-- <th>Category</th> --}}
                        <th>Description</th>
                        <th style="width: 5%;">Published</th>
                        <th style="width: 5%;">Action</th>
                    </tr>
                </thead>
                <tbody id="tblBodyProduct">
                    @foreach ($products as $u)
                        <tr>
                            <td></td>
                            <td>
                                <div class="attachment-block clearfix" style="text-align: center;">
                                    <img class="img-thumbnail" src="{{ route('main.getResource', ['id' => $u->mediaId]) }}"
                                        alt="attachment image">
                                </div>
                            </td>
                            <td>{{ $u->title }}</td>
                            {{-- <td>{{ $categories[$u->category] ?? "" }}</td> --}}
                            <td>{{ $u->description }}</td>
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
                                <a href="javascript:void(0);" class="text-success" title="Edit"
                                    onclick="editSelected('{{ encrypt($u->id) }}')">
                                    <i class="fa fa-edit" style="font-size: 20px;"></i>
                                </a>
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
                    <p>Are you sure to remove Product ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnRemoveYes">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modalFormProduct">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Form Product</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-6" >
                                    <div class="form-group">
                                        <label for="formTitle" class="col-sm-3 control-label">Title <span
                                                class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="formTitle" placeholder="Full Name"
                                                maxlength="100">
                                        </div>
                                    </div>

                                    {{-- <div class="form-group">
                                        <label for="formCategory" class="col-sm-3 control-label">Category <span
                                                class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <select id="formCategory" class="form-control">
                                                <option value="">Select Category</option>
                                                @foreach ($categories as $k => $v)
                                                    <option value="{{$k}}">{{$v}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}

                                    <div class="form-group">
                                        <label for="formDescription" class="col-sm-3 control-label">Description <span
                                                class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="hidden" id="formCategory" value="">
                                            <textarea class="form-control" id="formDescription"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="formImage" class="col-sm-3 control-label">Image <span
                                                class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="file" id="formImage" accept="image/png,image/jpg,image/jpeg">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="formPublish" class="col-sm-3 control-label">&nbsp;</label>
                                        <div class="col-sm-9">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" id="formPublish"> Publish
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6" >
                                    <img 
                                        class="img-responsive pad pull-right" 
                                        src="" 
                                        alt="Photo Product"
                                        style="max-width: 250px; margin-right:25px;"
                                        id="photo-product"
                                    >
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
        var noImage = '{{ asset("images/saf/no-image.png") }}';
        var Selected = "";
        $(document).ready(function() {
            $("#tblBodyProduct").find("tr").each(function(i, e) {
                $(this).find("td").eq(0).html(i + 1)
            });
            $("#tblProduct").DataTable();
            $("#photo-product").prop("src", noImage);

            $("#formImage").on("change", function() {
                const file = this.files && this.files[0] ? this.files[0] : null;
                if (!file) {
                    $("#photo-product").prop("src", noImage);
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    $("#photo-product").prop("src", e.target.result || noImage);
                };
                reader.onerror = function() {
                    $("#photo-product").prop("src", noImage);
                };
                reader.readAsDataURL(file);
            });

            $("#btnRemoveYes").click(function() {
                $.get('{{ url('/admin/product/remove') }}/' + Selected, function() {
                    $("#modalRemoveConfirm").modal("hide");
                    window.location.reload();
                })
                $("#modalRemoveConfirm").modal("hide")
            })

            $("#btnAddProduct").click(function() {
                clearForm();
                $("#modalFormProduct").modal("show");
            });

            $("#btnSaveForm").click(function() {
                const id = Selected;
                const title = $("#formTitle").val();
                const desc = $("#formDescription").val();
                const category = $("#formCategory").val();
                const images = $("#formImage").prop('files');
                const publish = $("#formPublish").is(":checked") ? 1 : 0;

                const allowExt = ["image/png", "image/jpg", "image/jpeg"];
                //|| category == ""
                if ((images.length < 1 && id == "") || title == "" || desc == "" ) {
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
                    // console.log(images[i].type)
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

                console.log(images);

                var form = new FormData();
                form.append('image', images.length ? images[0] : "");
                form.append('title', title);
                form.append('publish', publish);
                form.append('description', desc);
                form.append('category', category);
                form.append('id', id);

                $.ajax({
                    url: "{{ url('/admin/product/save') }}",
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
                        let errText = "Gagal Menyimpan form.";
                        if (e.responseJSON && e.responseJSON.errors) {
                            const firstKey = Object.keys(e.responseJSON.errors)[0];
                            if (firstKey && e.responseJSON.errors[firstKey] && e.responseJSON.errors[firstKey][0]) {
                                errText = e.responseJSON.errors[firstKey][0];
                            }
                        }

                        $.toast({
                            heading: 'Error',
                            text: errText,
                            showHideTransition: 'fade',
                            position: 'bottom-right',
                            icon: 'error'
                        })
                    }
                });

            });
        });

        function removeSelected(id) {
            Selected = id;
            $("#modalRemoveConfirm").modal("show")
        }

        function editSelected(id)
        {
            Selected = id;
            $.get("{{ url('/admin/product/get') }}/" + id, function(res) {
                const {title, category, description, mediaId, publish} = res
                $("#formTitle").val(title)
                $("#formCategory").val(category)
                $("#formDescription").val(description)
                $("#formPublish").prop("checked", publish == "1")
                if (mediaId) {
                    $("#photo-product").prop("src", "{{url('/getResource')}}/" + mediaId)
                } else {
                    $("#photo-product").prop("src", noImage)
                }
                $("#modalFormProduct").modal("show");
            });
        }

        function clearForm() {
            $("#photo-product").prop("src", noImage)
            Selected = "";
            $("#formImage, #formTitle, #formDescription").val("");
            $('#formPublish').prop('checked', false);
        }

        function unPublish(id) {
            $.get("{{ url('/admin/product/publish') }}/" + id + "?publish=0", function() {
                window.location.reload();
            });
        }

        function publish(id) {
            $.get("{{ url('/admin/product/publish') }}/" + id + "?publish=1", function() {
                window.location.reload();
            });
        }
    </script>
@endsection
