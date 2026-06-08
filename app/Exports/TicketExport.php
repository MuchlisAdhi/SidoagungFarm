<?php

namespace App\Exports;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Services\Admin\TicketManagementService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketExport implements FromArray, ShouldAutoSize, WithStyles
{
    protected array $filters;
    protected array $allowedQuestionTypes;
    protected TicketManagementService $ticketManagementService;

    public function __construct(
        array $filters = [],
        array $allowedQuestionTypes = [],
        ?TicketManagementService $ticketManagementService = null
    ) {
        $this->filters = $filters;
        $this->allowedQuestionTypes = $allowedQuestionTypes;
        $this->ticketManagementService = $ticketManagementService ?? app(TicketManagementService::class);
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

        $tickets = $this->fetchTickets();

        $number = 0;
        foreach ($tickets as $ticket) {
            $number++;

            $status = $ticket->status instanceof TicketStatus
                ? $ticket->status->value
                : (string) $ticket->status;

            $rows[] = [
                $number,
                $ticket->ticket_number,
                $ticket->requester_name,
                $ticket->requester_email,
                $this->formatPhone($ticket->requester_phone),
                $ticket->subject,
                $ticket->message,
                strtoupper($status),
                strtoupper((string) $ticket->priority),
                strtoupper((string) $ticket->channel),
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

    /**
     * Fetch tickets using the same service used by the index page so the
     * role-based question type filtering stays in sync.
     */
    protected function fetchTickets()
    {
        return $this->ticketManagementService
            ->list($this->filters, $this->allowedQuestionTypes)['tickets'];
    }

    /**
     * Prefix phone numbers with an apostrophe so Excel treats the cell as text
     * and the leading "0" (or "+") is preserved instead of being interpreted
     * as a number / scientific notation.
     */
    protected function formatPhone(?string $phone): string
    {
        $phone = trim((string) $phone);

        if ($phone === '') {
            return '';
        }

        return "'" . $phone;
    }
}