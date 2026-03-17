@extends('admin.master')
@section('page')
    Email Config
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-lg-12" style="margin-bottom: 5px;">
                    <button class="btn btn-sm btn-primary pull-right" id="btnAddConfig">Add Email Config</button>
                </div>
            </div>
            <table id="tblEmailConfig" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th>Name</th>
                        <th>Host</th>
                        <th style="width: 8%;">Port</th>
                        <th>Username</th>
                        <th>From</th>
                        <th>Admin Recipient</th>
                        <th style="width: 10%;">Encryption</th>
                        <th style="width: 8%;">Active</th>
                        <th style="width: 12%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $row)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->name ?: '-' }}</td>
                            <td>{{ $row->host }}</td>
                            <td>{{ $row->port }}</td>
                            <td>{{ $row->username }}</td>
                            <td>{{ $row->from_name }} ({{ $row->from_address }})</td>
                            <td>{{ $row->report ?: '-' }}</td>
                            <td>{{ $row->encryption ?: '-' }}</td>
                            <td style="text-align: center;">
                                @if ($row->is_active)
                                    <span class="label label-success">Yes</span>
                                @else
                                    <span class="label label-default">No</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0);" class="text-success" title="Edit"
                                    onclick="editSelected('{{ encrypt($row->id) }}')">
                                    <i class="fa fa-edit" style="font-size: 18px;"></i>
                                </a>
                                @if (! $row->is_active)
                                    <a href="javascript:void(0);" class="text-primary" title="Set Active"
                                        onclick="activateSelected('{{ encrypt($row->id) }}')">
                                        <i class="fa fa-check-circle" style="font-size: 18px;"></i>
                                    </a>
                                @endif
                                <a href="javascript:void(0);" class="text-danger" title="Delete"
                                    onclick="removeSelected('{{ encrypt($row->id) }}')">
                                    <i class="fa fa-trash" style="font-size: 18px;"></i>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Delete Confirm</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure to delete this email config?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="btnRemoveYes">Yes</button>
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
        var selected = "";
        $(function() {
            $("#tblEmailConfig").DataTable();
            $("#btnAddConfig").click(function() {
                window.location.href = "{{ url('/admin/email-config/add') }}";
            });

            $("#btnRemoveYes").click(function() {
                $.get("{{ url('/admin/email-config/delete') }}/" + selected, function() {
                    $("#modalRemoveConfirm").modal("hide");
                    window.location.reload();
                });
            });
        });

        function editSelected(id) {
            window.location.href = "{{ url('/admin/email-config/edit') }}/" + id;
        }

        function activateSelected(id) {
            $.get("{{ url('/admin/email-config/activate') }}/" + id, function() {
                window.location.reload();
            });
        }

        function removeSelected(id) {
            selected = id;
            $("#modalRemoveConfirm").modal("show");
        }
    </script>
@endsection
