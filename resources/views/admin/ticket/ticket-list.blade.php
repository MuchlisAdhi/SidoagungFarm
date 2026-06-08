@extends('admin.master')
@section('page')
    Tickets
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4 col-xs-12">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $stats['new'] }}</h3>
                    <p>New</p>
                </div>
                <div class="icon"><i class="fa fa-ticket"></i></div>
            </div>
        </div>
        <div class="col-lg-4 col-xs-12">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>{{ $stats['open'] }}</h3>
                    <p>Open</p>
                </div>
                <div class="icon"><i class="fa fa-envelope-open"></i></div>
            </div>
        </div>
        <div class="col-lg-4 col-xs-12">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $stats['responded'] }}</h3>
                    <p>Responded</p>
                </div>
                <div class="icon"><i class="fa fa-check-circle"></i></div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header">
            <form method="get" action="{{ url('/admin/ticket') }}" class="row" style="margin: 0;">
                <div class="col-lg-2 col-md-3 col-sm-6" style="padding-top: 8px;">
                    <label>Status</label>
                    <select class="form-control" name="status">
                        <option value="">All</option>
                        <option value="new" @selected(($filters['status'] ?? '') === 'new')>New</option>
                        <option value="open" @selected(($filters['status'] ?? '') === 'open')>Open</option>
                        <option value="responded" @selected(($filters['status'] ?? '') === 'responded')>Responded</option>
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
                    <a href="{{ url('/admin/ticket') }}" class="btn btn-default">Reset</a>
                    <a href="{{ url('/admin/ticket/export') . '?' . http_build_query($filters) }}" class="btn btn-success" title="Export to Excel">
                        <i class="fa fa-file-excel-o"></i> Export
                    </a>
                </div>
            </form>
        </div>
        <div class="box-body">
            <table id="tblTickets" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 14%;">Ticket No</th>
                        <th style="width: 18%;">Requester</th>
                        <th>Subject</th>
                        <th style="width: 9%;">Status</th>
                        <th style="width: 9%;">Priority</th>
                        <th style="width: 10%;">Channel</th>
                        <th style="width: 15%;">Created</th>
                        <th style="width: 6%;">Show</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $ticket)
                        @php
                            $status = $ticket->status instanceof \App\Enums\TicketStatus
                                ? $ticket->status->value
                                : $ticket->status;
                            $badge = 'label-default';
                            if ($status === 'new') {
                                $badge = 'label-warning';
                            } elseif ($status === 'open') {
                                $badge = 'label-primary';
                            } elseif ($status === 'responded') {
                                $badge = 'label-success';
                            }
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $ticket->ticket_number }}</td>
                            <td>
                                <strong>{{ $ticket->requester_name }}</strong><br>
                                <small>{{ $ticket->requester_email }}</small>
                            </td>
                            <td>{{ $ticket->subject }}</td>
                            <td style="text-align: center;">
                                <span class="label {{ $badge }}">{{ strtoupper($status) }}</span>
                            </td>
                            <td>{{ strtoupper($ticket->priority) }}</td>
                            <td>{{ strtoupper($ticket->channel) }}</td>
                            <td>{{ $ticket->created_at }}</td>
                            <td style="text-align: center;">
                                <a href="{{ url('/admin/ticket/show/' . encrypt($ticket->id)) }}" class="text-info">
                                    <i class="fa fa-eye" style="font-size: 18px;"></i>
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
            $("#tblTickets").DataTable();
        });
    </script>
@endsection
