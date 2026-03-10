@extends('admin.master')
@section('page')
    Ticket Detail
@endsection

@section('content')
    @php
        $status = $ticket->status instanceof \App\Enums\TicketStatus
            ? $ticket->status->value
            : $ticket->status;
    @endphp

    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-lg-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 35%;">Ticket Number</th>
                            <td>{{ $ticket->ticket_number }}</td>
                        </tr>
                        <tr>
                            <th>Requester Name</th>
                            <td>{{ $ticket->requester_name }}</td>
                        </tr>
                        <tr>
                            <th>Requester Email</th>
                            <td>{{ $ticket->requester_email }}</td>
                        </tr>
                        <tr>
                            <th>Requester Phone</th>
                            <td>{{ $ticket->requester_phone }}</td>
                        </tr>
                        <tr>
                            <th>Question Source</th>
                            <td>{{ strtoupper($ticket->question_mode ?? '-') }} #{{ $ticket->question_id ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 35%;">Created At</th>
                            <td>{{ $ticket->created_at }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $ticket->updated_at }}</td>
                        </tr>
                        <tr>
                            <th>Responded At</th>
                            <td>{{ $ticket->responded_at ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ strtoupper($status) }}</td>
                        </tr>
                        <tr>
                            <th>Priority</th>
                            <td>{{ strtoupper($ticket->priority) }}</td>
                        </tr>
                        <tr>
                            <th>Channel</th>
                            <td>{{ strtoupper($ticket->channel) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <label>Subject</label>
                <input type="text" class="form-control" value="{{ $ticket->subject }}" readonly>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea class="form-control" rows="4" readonly>{{ $ticket->message }}</textarea>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Response Ticket</h3>
        </div>
        <div class="box-body">
            <form method="post" action="{{ url('/wongelek/ticket/update/' . encrypt($ticket->id)) }}">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="formStatus">Status</label>
                            <select class="form-control" id="formStatus" name="formStatus">
                                <option value="new" @selected($status === 'new')>New</option>
                                <option value="open" @selected($status === 'open')>Open</option>
                                <option value="responded" @selected($status === 'responded')>Responded</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="formPriority">Priority</label>
                            <select class="form-control" id="formPriority" name="formPriority">
                                <option value="low" @selected($ticket->priority === 'low')>Low</option>
                                <option value="normal" @selected($ticket->priority === 'normal')>Normal</option>
                                <option value="high" @selected($ticket->priority === 'high')>High</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="formChannel">Channel</label>
                            <select class="form-control" id="formChannel" name="formChannel">
                                <option value="website" @selected($ticket->channel === 'website')>Website</option>
                                <option value="email" @selected($ticket->channel === 'email')>Email</option>
                                <option value="phone" @selected($ticket->channel === 'phone')>Phone</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="formResponse">Response Message</label>
                    <textarea class="form-control" id="formResponse" name="formResponse" rows="5">{{ old('formResponse', $ticket->response_message) }}</textarea>
                </div>
                <div class="text-right">
                    <a href="{{ url('/wongelek/ticket') }}" class="btn btn-default">Back</a>
                    <button type="submit" class="btn btn-success">Save Ticket</button>
                </div>
            </form>
        </div>
    </div>
@endsection
