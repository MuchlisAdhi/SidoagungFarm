@extends('admin.master')
@section('page')
    Lowongan Kerja
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-lg-12" style="margin-bottom: 5px;">
                    <button class="btn btn-sm btn-primary pull-right" id="btnAddCareer">Add Career</button>
                </div>
            </div>
            <table id="tblCareer" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th>Posisi</th>
                        <th style="width: 20%;">Lokasi</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Closing</th>
                        {{-- <th style="width: 5%;">Pelamar</th> --}}
                        <th style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="tblBodyCareer">
                    @foreach ($list as $l)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{ $l->position }}</td>
                            <td>{{ $l->location }}</td>
                            <td>
                                @if($l->publish)
                                    <span class="text-success text-bold">Open</span>
                                @else
                                <span class="text-danger text-bold">Close</span>
                                @endif
                            </td>
                            <td>{{ $l->closingdate }}</td>
                            {{-- <td>{{ 0 }}</td> --}}
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
                    <p>Are you sure to remove Career ?</p>
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
            $("#tblCareer").DataTable();
            $("#btnAddCareer").click(function(){
                window.location.href = "{{url('/admin/karir/add')}}"
            })

            $("#btnRemoveYes").click(function() {
                $.get('{{ url('/admin/karir/delete') }}/' + selected, function() {
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
            window.location.href = "{{url('/admin/karir/edit')}}/" + id;
        }
        
    </script>
@endsection