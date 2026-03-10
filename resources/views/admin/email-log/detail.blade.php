@extends('admin.master')
@section('page')
    Email Log Detail
@endsection

@section('content')
    <div class="box">
        <div class="box-body">
            <table class="table table-bordered">
                <tr>
                    <th style="width: 25%;">Log ID</th>
                    <td>{{ $row->id }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $row->created_at }}</td>
                </tr>
                <tr>
                    <th>Ticket No</th>
                    <td>{{ $row->ticket_no ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Question Source</th>
                    <td>{{ strtoupper($row->question_mode ?: '-') }} #{{ $row->question_id ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Recipient</th>
                    <td>{{ $row->recipient_email }}</td>
                </tr>
                <tr>
                    <th>Subject</th>
                    <td>{{ $row->subject }}</td>
                </tr>
                <tr>
                    <th>Template</th>
                    <td>{{ $row->template }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>{{ strtoupper($row->status) }}</td>
                </tr>
                <tr>
                    <th>Sent At</th>
                    <td>{{ $row->sent_at ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Error Message</th>
                    <td>
                        @if ($row->error_message)
                            <pre style="white-space: pre-wrap;">{{ $row->error_message }}</pre>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>

            <div class="form-group">
                <label>Email Body (HTML)</label>
                <textarea class="form-control" rows="12" readonly>{{ $row->body }}</textarea>
            </div>

            <div class="text-right">
                <a href="{{ url('/wongelek/email-log') }}" class="btn btn-default">Back</a>
            </div>
        </div>
    </div>
@endsection
