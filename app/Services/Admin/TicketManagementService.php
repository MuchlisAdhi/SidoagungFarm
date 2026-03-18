<?php

namespace App\Services\Admin;

use App\Enums\QuestionType;
use App\Enums\TicketStatus;
use App\Jobs\NotificationJob;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

class TicketManagementService
{
    public function list(array $filters, array $allowedQuestionTypes = []): array
    {
        $allowedQuestionTypes = QuestionType::normalize($allowedQuestionTypes);

        $status = (string) ($filters['status'] ?? '');
        $dateFrom = (string) ($filters['date_from'] ?? '');
        $dateTo = (string) ($filters['date_to'] ?? '');
        $ticketNo = trim((string) ($filters['ticket_no'] ?? ''));

        $query = Ticket::query();
        $this->applyQuestionTypeScope($query, $allowedQuestionTypes);
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

        $statsBaseQuery = Ticket::query();
        $this->applyQuestionTypeScope($statsBaseQuery, $allowedQuestionTypes);

        return [
            'tickets' => $query->orderByDesc('created_at')->get(),
            'stats' => [
                'new' => (clone $statsBaseQuery)->where('status', TicketStatus::New->value)->count(),
                'open' => (clone $statsBaseQuery)->where('status', TicketStatus::Open->value)->count(),
                'responded' => (clone $statsBaseQuery)->where('status', TicketStatus::Responded->value)->count(),
            ],
            'filters' => [
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'ticket_no' => $ticketNo,
            ],
        ];
    }

    public function findByEncryptedId(string $encryptedId, array $allowedQuestionTypes = []): ?Ticket
    {
        $allowedQuestionTypes = QuestionType::normalize($allowedQuestionTypes);

        $id = $this->decryptId($encryptedId);
        if (! $id) {
            return null;
        }

        $ticket = Ticket::find($id);
        if (! $ticket || ! $this->hasQuestionTypeAccess($ticket, $allowedQuestionTypes)) {
            return null;
        }

        return $ticket;
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

    protected function applyQuestionTypeScope(Builder $query, array $allowedQuestionTypes): void
    {
        if (QuestionType::isAll($allowedQuestionTypes)) {
            return;
        }

        if ($allowedQuestionTypes === []) {
            $query->whereRaw('1 = 0');
            return;
        }

        $includeProduct = in_array(QuestionType::Produk->value, $allowedQuestionTypes, true);

        $query->where(function (Builder $scopedQuery) use ($includeProduct, $allowedQuestionTypes) {
            if ($includeProduct) {
                $scopedQuery->where('question_mode', 'q1');
            }

            $method = $includeProduct ? 'orWhere' : 'where';
            $scopedQuery->{$method}(function (Builder $q2Query) use ($allowedQuestionTypes) {
                $q2Query->where('question_mode', 'q2')
                    ->whereIn('subject', $allowedQuestionTypes);
            });
        });
    }

    protected function hasQuestionTypeAccess(Ticket $ticket, array $allowedQuestionTypes): bool
    {
        if (QuestionType::isAll($allowedQuestionTypes)) {
            return true;
        }

        if ((string) $ticket->question_mode === 'q1') {
            return in_array(QuestionType::Produk->value, $allowedQuestionTypes, true);
        }

        return in_array((string) $ticket->subject, $allowedQuestionTypes, true);
    }
}
