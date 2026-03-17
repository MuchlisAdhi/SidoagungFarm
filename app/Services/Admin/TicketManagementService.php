<?php

namespace App\Services\Admin;

use App\Enums\TicketStatus;
use App\Jobs\NotificationJob;
use App\Models\Ticket;
use Throwable;

class TicketManagementService
{
    public function list(array $filters): array
    {
        $status = (string) ($filters['status'] ?? '');
        $dateFrom = (string) ($filters['date_from'] ?? '');
        $dateTo = (string) ($filters['date_to'] ?? '');
        $ticketNo = trim((string) ($filters['ticket_no'] ?? ''));

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

        return [
            'tickets' => $query->orderByDesc('created_at')->get(),
            'stats' => [
                'new' => Ticket::where('status', TicketStatus::New->value)->count(),
                'open' => Ticket::where('status', TicketStatus::Open->value)->count(),
                'responded' => Ticket::where('status', TicketStatus::Responded->value)->count(),
            ],
            'filters' => [
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'ticket_no' => $ticketNo,
            ],
        ];
    }

    public function findByEncryptedId(string $encryptedId): ?Ticket
    {
        $id = $this->decryptId($encryptedId);
        if (! $id) {
            return null;
        }

        return Ticket::find($id);
    }

    public function update(Ticket $ticket, array $validated): string
    {
        $response = trim((string) ($validated['formResponse'] ?? ''));
        $newStatus = (string) $validated['formStatus'];

        if ($response !== '' && $newStatus !== TicketStatus::Responded->value) {
            $newStatus = TicketStatus::Responded->value;
        }

        $payload = [
            'status' => $newStatus,
            'priority' => (string) $validated['formPriority'],
            'channel' => (string) $validated['formChannel'],
            'response_message' => $response !== '' ? $response : $ticket->response_message,
        ];

        if ($newStatus === TicketStatus::Responded->value && ! $ticket->responded_at) {
            $payload['responded_at'] = now();
        }

        $ticket->update($payload);
        $ticket->refresh();

        $warning = '';
        if (
            ($ticket->status?->value ?? '') === TicketStatus::Responded->value &&
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
            } catch (Throwable $th) {
                report($th);
                $warning = ' Namun email response gagal dikirim.';
            }
        }

        return $warning;
    }

    protected function decryptId(string $encryptedId): ?string
    {
        try {
            return (string) decrypt($encryptedId);
        } catch (Throwable) {
            return null;
        }
    }
}
