@extends('admin.master')
@section('page')
    Menjadi Mitra
@endsection

@php
    $categories = [
        'feed'   => "Pakan Ternak",
        'doc'     => "Bibit Ayam Umur Sehari",
        'livebird'     => "Ayam Hidup",
        'broiler'    => "Ayam Potong",
        'Kemitraan' => "Kemitraan",
        'kemitraan' => "Kemitraan",
        'Menjadi Mitra' => "Menjadi Mitra",
        'menjadi mitra' => "Menjadi Mitra",
    ];
@endphp

@section('content')
    <div class="box">
        <div class="box-body">
            <table id="tblMitra" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 20%;">Name</th>
                        <th style="width: 20%;">Email</th>
                        <th style="width: 20%;">Phone</th>
                        <th style="width: 20%;">Category</th>
                        <th>Company</th>
                        <th>Location</th>
                        <th style="width: 5%;">Status</th>
                        <th style="width: 5%;">Show</th>
                    </tr>
                </thead>
                <tbody id="tblBodyMitra">
                    @foreach ($list as $u)
                        <tr>
                            <td></td>
                            <td>{{ $u->firstname . " " . $u->lastname }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->phone }}</td>
                            <td>{{ $categories[$u->category] ?? $u->category ?? '-' }}</td>
                            <td>{{ $u->companyname }}</td>
                            <td>{{ $u->companylocation }}</td>
                            <td>
                                @if($u->replied)
                                <span class="text-success">Replied</span>
                                @else
                                <span class="text-warning">New</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0);" class="text-success" title="Show"
                                    onclick="viewSelected('{{ encrypt($u->id) }}')">
                                    <i class="fa fa-eye" style="font-size: 20px;"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal" id="modalFormMitra">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Menjadi Mitra</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="formName" class="col-sm-3 control-label">Name</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formName" >
                                        </div>
                                    </div>
        
                                    <div class="form-group">
                                        <label for="formBod" class="col-sm-3 control-label">BOD</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formBod" >
                                        </div>
                                    </div>
        
                                    <div class="form-group">
                                        <label for="formEmail" class="col-sm-3 control-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formEmail" >
                                        </div>
                                    </div>
        
                                    <div class="form-group">
                                        <label for="formPhone" class="col-sm-3 control-label">Phone</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formPhone" >
                                        </div>
                                    </div>
        
                                    <div class="form-group">
                                        <label for="formCategory" class="col-sm-3 control-label">Category</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formCategory" >
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="formCompany" class="col-sm-3 control-label">Company</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formCompany" >
                                        </div>
                                    </div>
        
                                    <div class="form-group">
                                        <label for="formLocation" class="col-sm-3 control-label">Location</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control input-sm" id="formLocation" >
                                        </div>
                                    </div>
        
                                    <div class="form-group">
                                        <label for="formDescription" class="col-sm-3 control-label">Description</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control input-sm" id="formDescription"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" id="btnReplied" class="btn btn-success btn-sm">Make as replied</button>
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
        var categories = {!! json_encode($categories) !!};
        var selected = "";
        $(document).ready(function() {
            $("#tblBodyMitra").find("tr").each(function(i, e) {
                $(this).find("td").eq(0).html(i + 1)
            });
            $("#tblMitra").DataTable();
            $("#btnReplied").click(function(){
                replied();
            });
        });

        function clearForm() {
            selected = "";
            $("#formImage, #formTitle").val("");
            $('#formPublish').prop('checked', false);
        }

        function replied() {
            let id = selected;
            if (!id) {
                return;
            }

            if (!AdminSubmit.start("#btnReplied", "Menyimpan...")) {
                return;
            }

            $.get("{{ url('/admin/feedback/mitra/replied') }}/" + id , function() {
                window.location.reload();
            }).fail(function() {
                AdminSubmit.stop("#btnReplied");
                $.toast({
                    heading: 'Error',
                    text: 'Gagal mengubah status mitra.',
                    showHideTransition: 'fade',
                    position: 'bottom-right',
                    icon: 'error'
                });
            });
        }

        function viewSelected(id)
        {
            selected = id;
            $.get("{{ url('/admin/feedback/mitra/get') }}/" + id , function(res) {
                const {code, msg, data} = res;
                const {firstname, lastname, bod, email, phone, category, companyname, companylocation, companydescription} = data
                $("#formName").val(firstname + " " + lastname);
                $("#formBod").val(bod);
                $("#formEmail").val(email);
                $("#formPhone").val(phone);
                $("#formCategory").val(categories[data.category] || data.category || "-");
                $("#formCompany").val(companyname);
                $("#formLocation").val(companylocation);
                $("#formDescription").val(companydescription);
                $("#modalFormMitra").modal("show")
            });
        }
    </script>
@endsection
