@extends('admin.master')
@section('page')
    Email Logs
@endsection

@section('content')
    <div class="box">
        <div class="box-header">
            <form method="get" action="{{ url('/wongelek/email-log') }}" class="row" style="margin: 0;">
                <div class="col-lg-2 col-md-3 col-sm-6" style="padding-top: 8px;">
                    <label>Status</label>
                    <select class="form-control" name="status">
                        <option value="">All</option>
                        <option value="queued" @selected(($filters['status'] ?? '') === 'queued')>Queued</option>
                        <option value="sent" @selected(($filters['status'] ?? '') === 'sent')>Sent</option>
                        <option value="failed" @selected(($filters['status'] ?? '') === 'failed')>Failed</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6" style="padding-top: 8px;">
                    <label>Date From</label>
                    <input type="date" class="form-control" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div class="col-lg-2 col-md-3 col-sm-6" style="padding-top: 8px;">
                    <label>Date To</label>
                    <input type="date" class="form-control" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6" style="padding-top: 8px;">
                    <label>Ticket Number</label>
                    <input type="text" class="form-control" name="ticket_no" value="{{ $filters['ticket_no'] ?? '' }}" placeholder="Search ticket number">
                </div>
                <div class="col-lg-3 col-md-12 col-sm-12" style="padding-top: 30px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                    <a href="{{ url('/wongelek/email-log') }}" class="btn btn-default">Reset</a>
                </div>
            </form>
        </div>
        <div class="box-body">
            <table id="tblEmailLogs" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 15%;">Date</th>
                        <th style="width: 14%;">Ticket</th>
                        <th style="width: 10%;">Mode</th>
                        <th style="width: 5%;">QID</th>
                        <th style="width: 18%;">Recipient</th>
                        <th style="width: 15%;">Template</th>
                        <th>Subject</th>
                        <th style="width: 8%;">Status</th>
                        <th style="width: 6%;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $row)
                        @php
                            $badge = 'label-default';
                            if ($row->status === 'sent') {
                                $badge = 'label-success';
                            } elseif ($row->status === 'failed') {
                                $badge = 'label-danger';
                            } elseif ($row->status === 'queued') {
                                $badge = 'label-warning';
                            }
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $row->created_at }}</td>
                            <td>{{ $row->ticket_no ?: '-' }}</td>
                            <td>{{ strtoupper($row->question_mode ?: '-') }}</td>
                            <td>{{ $row->question_id ?: '-' }}</td>
                            <td>{{ $row->recipient_email }}</td>
                            <td>{{ $row->template }}</td>
                            <td>{{ $row->subject }}</td>
                            <td style="text-align:center;">
                                <span class="label {{ $badge }}">{{ strtoupper($row->status) }}</span>
                            </td>
                            <td style="text-align:center;">
                                <a href="{{ url('/wongelek/email-log/show/' . encrypt($row->id)) }}" class="text-info">
                                    <i class="fa fa-eye" style="font-size:18px;"></i>
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
        $(function() {
            $("#tblEmailLogs").DataTable();
        });
    </script>
@endsection
