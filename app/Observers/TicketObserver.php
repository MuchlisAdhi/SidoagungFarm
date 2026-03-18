<?php

namespace App\Observers;

use App\Enums\TicketStatus;
use App\Models\ClientQuestion;
use App\Models\ClientQuestion2;
use App\Models\Ticket;

class TicketObserver
{
    public function saved(Ticket $ticket): void
    {
        if (! in_array($ticket->question_mode, ['q1', 'q2'], true)) {
            return;
        }

        $questionId = (string) $ticket->question_id;
        if ($questionId === '') {
            return;
        }

        $status = $ticket->status?->value ?? TicketStatus::New->value;
        $payload = [
            'ticket_no' => (string) ($ticket->ticket_number ?: ''),
            'ticket_status' => $status,
            'replied' => $status === TicketStatus::Responded->value ? 1 : 0,
        ];

        if ($ticket->question_mode === 'q1') {
            ClientQuestion::where('id', $questionId)->update($payload);
            return;
        }

        ClientQuestion2::where('id', $questionId)->update($payload);
    }
}
