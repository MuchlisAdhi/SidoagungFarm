<?php

namespace App\Exports;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected array $filters;
    protected array $allowedQuestionTypes;

    public function __construct(array $filters = [], array $allowedQuestionTypes = [])
    {
        $this->filters = $filters;
        $this->allowedQuestionTypes = $allowedQuestionTypes;
    }

    public function array(): array
    {
        $rows = [[
            'No.',
            'Ticket No',
            'Requester Name',
            'Requester Email',
            'Requester Phone',
            'Subject',
            'Message',
            'Status',
            'Priority',
            'Channel',
            'Response Message',
            'Responded At',
            'Created At',
        ]];

        $tickets = $this->buildQuery()->orderByDesc('created_at')->get();

        $number = 0;
        foreach ($tickets as $ticket) {
            $number++;

            $status = $ticket->status instanceof \App\Enums\TicketStatus
                ? $ticket->status->value
                : (string) $ticket->status;

            $rows[] = [
                $number,
                $ticket->ticket_number,
                $ticket->requester_name,
                $ticket->requester_email,
                $ticket->requester_phone,
                $ticket->subject,
                $ticket->message,
                strtoupper($status),
                strtoupper($ticket->priority),
                strtoupper($ticket->channel),
                $ticket->response_message,
                $ticket->responded_at ? $ticket->responded_at->format('Y-m-d H:i:s') : '',
                $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i:s') : '',
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    protected function buildQuery(): Builder
    {
        $query = Ticket::query();

        $status = (string) ($this->filters['status'] ?? '');
        $dateFrom = (string) ($this->filters['date_from'] ?? '');
        $dateTo = (string) ($this->filters['date_to'] ?? '');
        $ticketNo = trim((string) ($this->filters['ticket_no'] ?? ''));

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

        return $query;
    }
}