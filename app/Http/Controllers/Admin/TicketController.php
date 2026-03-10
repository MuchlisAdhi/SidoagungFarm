<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTicketRequest;
use App\Jobs\NotificationJob;
use App\Models\ClientQuestion;
use App\Models\ClientQuestion2;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function list()
    {
        $status = (string) request()->query('status', '');
        $dateFrom = (string) request()->query('date_from', '');
        $dateTo = (string) request()->query('date_to', '');
        $ticketNo = trim((string) request()->query('ticket_no', ''));

        $query = Ticket::query();
        if (in_array($status, ['new', 'open', 'responded'], true)) {
            $query->where('status', $status);
        }
        if ($ticketNo !== '') {
            $query->where('ticket_number', 'like', '%' . $ticketNo . '%');
        }
        if ($dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $tickets = $query->orderByDesc('created_at')->get();

        $stats = [
            'new' => Ticket::where('status', TicketStatus::New->value)->count(),
            'open' => Ticket::where('status', TicketStatus::Open->value)->count(),
            'responded' => Ticket::where('status', TicketStatus::Responded->value)->count(),
        ];

        $filters = [
            'status' => $status,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'ticket_no' => $ticketNo,
        ];

        return view('admin.ticket.ticket-list', compact('tickets', 'stats', 'filters'));
    }

    public function show($id)
    {
        try {
            $id = decrypt($id);
            $ticket = Ticket::where('id', $id)->firstOrFail();
        } catch (\Throwable $th) {
            return redirect('/wongelek/ticket')->with('error', 'Ticket tidak ditemukan.');
        }

        return view('admin.ticket.ticket-show', compact('ticket'));
    }

    public function update(UpdateTicketRequest $request, $id)
    {
        try {
            $id = decrypt($id);
            $ticket = Ticket::where('id', $id)->firstOrFail();
        } catch (\Throwable $th) {
            return redirect('/wongelek/ticket')->with('error', 'Ticket tidak ditemukan.');
        }

        $validated = $request->validated();

        $response = trim((string) ($validated['formResponse'] ?? ''));
        $newStatus = $validated['formStatus'];

        if ($response !== '' && $newStatus !== TicketStatus::Responded->value) {
            $newStatus = TicketStatus::Responded->value;
        }

        $payload = [
            'status' => $newStatus,
            'priority' => $validated['formPriority'],
            'channel' => $validated['formChannel'],
            'response_message' => $response !== '' ? $response : $ticket->response_message,
        ];

        if ($newStatus === TicketStatus::Responded->value && ! $ticket->responded_at) {
            $payload['responded_at'] = now();
        }

        $ticket->update($payload);

        if ($ticket->question_mode === 'q1') {
            ClientQuestion::where('id', $ticket->question_id)->update([
                'ticket_status' => $ticket->status->value,
                'replied' => $ticket->status->value === TicketStatus::Responded->value ? 1 : 0,
                'response_message' => $ticket->response_message,
                'responded_at' => $ticket->responded_at,
            ]);
        }

        if ($ticket->question_mode === 'q2') {
            ClientQuestion2::where('id', $ticket->question_id)->update([
                'ticket_status' => $ticket->status->value,
                'replied' => $ticket->status->value === TicketStatus::Responded->value ? 1 : 0,
                'response_message' => $ticket->response_message,
                'responded_at' => $ticket->responded_at,
            ]);
        }

        $warning = '';
        if (
            $ticket->status->value === TicketStatus::Responded->value &&
            $ticket->response_message &&
            $ticket->requester_email
        ) {
            try {
                NotificationJob::dispatch(
                    notificationType: 'ticket-responded',
                    questionMode: (string) $ticket->question_mode,
                    questionId: (string) $ticket->question_id,
                    ticketId: (string) $ticket->id
                );
            } catch (\Throwable $th) {
                report($th);
                $warning = ' Namun email response gagal dikirim.';
            }
        }

        return redirect('/wongelek/ticket/show/' . encrypt($ticket->id))
            ->with('success', 'Ticket berhasil diperbarui.' . $warning);
    }
}
