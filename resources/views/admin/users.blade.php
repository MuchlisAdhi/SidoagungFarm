@extends('admin.master')
@section('page')
    Users
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-lg-12" style="margin-bottom: 5px;">
                    <button class="btn btn-sm btn-primary pull-right" id="btnAddUser">Add User</button>
                </div>
            </div>
            <table id="tblUser" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Navigation Access</th>
                        <th style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="tblBodyUser">
                    @foreach ($users as $u)
                        <tr>
                            <td></td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->roles->pluck('name')->join(', ') ?: '-' }}</td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0);" class="btnEdit" title="Edit"
                                    onclick="editSelected('{{ encrypt($u->id) }}')">
                                    <i class="fa fa-edit" style="font-size: 20px;"></i>
                                </a>
                                &nbsp;
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
                    <p>Are you sure to remove User ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnRemoveYes">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modalFormUser">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Form User</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="formUserFullName" class="col-sm-3 control-label">User Full Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="formUserFullName" placeholder="Full Name" maxlength="30">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="formEmail" class="col-sm-3 control-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="formEmail" placeholder="Email" maxlength="30">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="formPassword" class="col-sm-3 control-label">Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="formPassword" placeholder="Password" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="formNavigationAccess" class="col-sm-3 control-label">Navigation Access</label>
                                <div class="col-sm-9">
                                    <select class="form-control" id="formNavigationAccess">
                                        <option value="">Select Navigation Access</option>
                                        @foreach ($navigationAccesses as $access)
                                            <option value="{{ $access['id'] }}">{{ $access['name'] }}</option>
                                        @endforeach
                                    </select>
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
        var userSelected = "";
        $(document).ready(function() {
            $("#tblBodyUser").find("tr").each(function(i, e) {
                $(this).find("td").eq(0).html(i + 1)
            });
            $("#tblUser").DataTable();

            $("#btnRemoveYes").click(function() {
                $.get('{{url('/admin/users/remove/')}}/' + userSelected, function(){
                    $("#modalRemoveConfirm").modal("hide");
                    window.location.reload();
                })
                $("#modalRemoveConfirm").modal("hide")
            })

            $("#btnAddUser").click(function() {
                clearForm();
                $("#modalFormUser").modal("show");
            });

            $("#btnSaveForm").click(function(){
                const id = userSelected;
                const fullname = ($("#formUserFullName").val() || "").trim()
                const email = ($("#formEmail").val() || "").trim()
                const pass = $("#formPassword").val() || ""
                const navigation_access = $("#formNavigationAccess").val()
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
                const strongPasswordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/;

                if (fullname === "" || email === "") {
                    $.toast({
                        heading: 'Error',
                        text: "Nama dan email wajib diisi.",
                        showHideTransition: 'fade',
                        position: 'bottom-right',
                        icon: 'error'
                    })
                    return;
                }

                if (!emailRegex.test(email)) {
                    $.toast({
                        heading: 'Error',
                        text: "Format email tidak valid.",
                        showHideTransition: 'fade',
                        position: 'bottom-right',
                        icon: 'error'
                    })
                    return;
                }

                if (id === "" && pass === "") {
                    $.toast({
                        heading: 'Error',
                        text: "Password tidak boleh kosong.",
                        showHideTransition: 'fade',
                        position: 'bottom-right',
                        icon: 'error'
                    })
                    return;
                }

                if (pass !== "" && !strongPasswordRegex.test(pass)) {
                    $.toast({
                        heading: 'Error',
                        text: "Password wajib kombinasi huruf, angka, simbol dan minimal 8 karakter.",
                        showHideTransition: 'fade',
                        position: 'bottom-right',
                        icon: 'error'
                    })
                    return;
                }

                if (!AdminSubmit.start("#btnSaveForm", "Menyimpan...")) {
                    return;
                }

                $.post('{{url("/admin/users/save")}}', {id, fullname, email, pass, navigation_access})
                .done(function(res){
                    if(res.code == 200)
                    {
                        window.location.reload();
                    }else{
                        AdminSubmit.stop("#btnSaveForm");
                        $.toast({
                            heading: 'Error',
                            text: res.msg,
                            showHideTransition: 'fade',
                            position: 'bottom-right',
                            icon: 'error'
                        })
                    }
                })
                .fail(function(xhr){
                    AdminSubmit.stop("#btnSaveForm");
                    let errText = "Gagal Menyimpan form.";
                    if (xhr && xhr.responseJSON && xhr.responseJSON.errors) {
                        const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                        if (firstKey && xhr.responseJSON.errors[firstKey] && xhr.responseJSON.errors[firstKey][0]) {
                            errText = xhr.responseJSON.errors[firstKey][0];
                        }
                    }

                    $.toast({
                        heading: 'Error',
                        text: errText,
                        showHideTransition: 'fade',
                        position: 'bottom-right',
                        icon: 'error'
                    })
                })
            });
        });

        function editSelected(id) {
            clearForm();
            userSelected = id;

            $.ajax({
                url : '{{url('/admin/users/getOne/')}}/' + id,
                success : function(res){
                    const {data, code} = res;
                    if(code == 200)
                    {
                        $("#formUserFullName").val(data.name).attr('disabled','disabled');
                        $("#formEmail").val(data.email).attr('disabled','disabled');
                        $("#formNavigationAccess").val(data.navigation_access || "");
                        $("#modalFormUser").modal("show");
                    }else{
                        $.toast({
                            heading: 'Error',
                            text: res.msg,
                            showHideTransition: 'fade',
                            position: 'bottom-right',
                            icon: 'error'
                        })
                    }
                },
                error: function(xhr, status, error){
                    $.toast({
                        heading: 'Error',
                        text: 'Gagal menampilkan data, coba beberapa saat lagi.',
                        showHideTransition: 'fade',
                        position: 'bottom-right',
                        icon: 'error'
                    })
                }
            })
        }

        function removeSelected(id) {
            userSelected = id;
            $("#modalRemoveConfirm").modal("show")
        }

        function clearForm()
        {
            userSelected = "";
            $("#formUserFullName, #formEmail, #formPassword, #formNavigationAccess").val("");
            $("#formUserFullName, #formEmail, #formPassword").removeAttr('disabled');
        }
    </script>
@endsection
