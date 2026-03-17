@extends('admin.master')
@section('page')
    Lowongan Kerja
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <table id="tblCareer" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th>Posisi</th>
                        <th style="width: 20%;">Lokasi</th>
                        <th style="width: 15%;">Closing</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 5%;">Pelamar</th>
                        <th style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="tblBodyCareer">
                    @foreach ($list as $l)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $l->position }}</td>
                            <td>{{ $l->location }}</td>
                            <td>{{ $l->closingdate }}</td>
                            <td>
                                @if ($l->publish)
                                    <span class="text-success text-bold">Open</span>
                                @else
                                    <span class="text-danger text-bold">Close</span>
                                @endif
                            </td>
                            <td>{{ $l->applicants }}</td>
                            <td style="text-align: center;">
                                <a href="javascript:void(0);" class="text-success" title="Edit"
                                    onclick="show('{{ encrypt($l->id) }}')">
                                    <i class="fa fa-eye" style="font-size: 20px;"></i>
                                </a>
                                <a href="javascript:void(0);" class="text-success" title="Export"
                                    onclick="show('{{ encrypt($l->id) }}')">
                                    <i class="fa fa-file-excel" style="font-size: 20px;"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
        $(function() {
            $("#tblCareer").DataTable();
        })

        function show(id) {
            window.location.href = "{{ url('/admin/feedback/karir/applicants') }}/" + id;
        }
    </script>
@endsection
