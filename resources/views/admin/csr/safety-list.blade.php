@extends('admin.master')
@section('page')
    CSR => Keselamatan Kerja
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-lg-12" style="margin-bottom: 5px;">
                    <button class="btn btn-sm btn-primary pull-right" id="btnAddSafety">Add Keselamatan Kerja</button>
                </div>
            </div>
            <table id="tblSafety" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 20%;">Thumbnail</th>
                        <th>Title</th>
                        <th style="width: 15%;">Release</th>
                        <th style="width: 15%;">Viewer</th>
                        <th style="width: 5%;">Publish</th>
                        <th style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="tblBodySafety">
                    @foreach ($list as $l)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>
                                <div class="attachment-block clearfix" style="text-align: center;">
                                    <img class="img-responsive" src="{{ route('main.getResource', ['id' => $l->thumbnail]) }}"
                                        alt="attachment image">
                                </div>
                            </td>
                            <td>{{ $l->title }}</td>
                            <td>{{ $l->releasedate }}</td>
                            <td>{{ $l->viewer }}</td>
                            <td style="text-align: center;">
                                @if ($l->publish)
                                    <a href="javascript:void(0)" onclick="unPublish('{{ encrypt($l->id) }}')">
                                        <i class="fa fa-check-circle-o" style="font-size: 20px;"></i>
                                    </a>
                                @else
                                    <a href="javascript:void(0)" onclick="publish('{{ encrypt($l->id) }}')">
                                        <i class="fa fa-circle-o text-danger" style="font-size: 20px;"></i>
                                    </a>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0);" class="text-success" title="Edit"
                                    onclick="editSelected('{{ encrypt($l->id) }}')">
                                    <i class="fa fa-edit" style="font-size: 20px;"></i>
                                </a>
                                <a href="javascript:void(0);" class="btnRemove text-danger" title="Remove"
                                    onclick="removeSelected('{{ encrypt($l->id) }}')">
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
                    <p>Are you sure to remove Safety ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnRemoveYes">Yes</button>
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
        var selected;
        $(function(){
            $("#tblSafety").DataTable();
            $("#btnAddSafety").click(function(){
                window.location.href = "{{url('/admin/csr/safety/add')}}"
            })

            $("#btnRemoveYes").click(function() {
                $.get("{{ url('/admin/csr/safety/delete') }}/" + selected, function() {
                    $("#modalRemoveConfirm").modal("hide");
                    window.location.reload();
                })
                $("#modalRemoveConfirm").modal("hide")
            })
        })

        function removeSelected(id) {
            selected = id;
            $("#modalRemoveConfirm").modal("show")
        }

        function editSelected(id) {
            window.location.href = "{{url('/admin/csr/safety/edit')}}/" + id;
        }

        function unPublish(id)
        {
            $.get("{{ url('/admin/csr/safety/publish')}}/" + id + "?publish=0", function() {
                window.location.reload();
            });
        }

        function publish(id)
        {
            $.get("{{ url('/admin/csr/safety/publish')}}/" + id + "?publish=1", function() {
                window.location.reload();
            });
        }
        
    </script>
@endsection