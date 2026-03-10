<?php

namespace App\Jobs;

use App\Models\ClientQuestion;
use App\Models\ClientQuestion2;
use App\Models\Product;
use App\Models\Ticket;
use App\Services\PhpMailerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $notificationType,
        public string $questionMode,
        public string $questionId,
        public ?string $ticketId = null
    ) {
        $this->onQueue('emails');
    }

    public function handle(PhpMailerService $phpMailerService): void
    {
        if (! in_array($this->notificationType, ['ticket-created', 'ticket-responded'], true)) {
            return;
        }

        if (! in_array($this->questionMode, ['q1', 'q2'], true)) {
            return;
        }

        $question = $this->questionMode === 'q1'
            ? ClientQuestion::find($this->questionId)
            : ClientQuestion2::find($this->questionId);

        if (! $question || ! $question->email) {
            return;
        }

        $ticket = $this->resolveTicket((string) $question->id);

        if ($this->notificationType === 'ticket-created') {
            $questionType = $this->resolveQuestionType($question);
            $ticketNo = (string) ($question->ticket_no ?: ($ticket?->ticket_number ?: '-'));

            $phpMailerService->sendTicketCreatedNotificationData(
                customerName: (string) ($question->name ?: 'Customer'),
                customerEmail: (string) $question->email,
                ticketNo: $ticketNo,
                questionType: $questionType,
                questionMessage: (string) ($question->description ?: '-'),
                questionMode: $this->questionMode,
                questionId: (string) $question->id,
                ticketId: $ticket?->id
            );

            try {
                $phpMailerService->sendNotificationAdmin(
                    requesterName: (string) ($question->name ?: 'Customer'),
                    requesterEmail: (string) ($question->email ?: '-'),
                    requesterPhone: $question->phone ?: null,
                    ticketNo: $ticketNo,
                    questionType: $questionType,
                    questionMessage: (string) ($question->description ?: '-'),
                    questionMode: $this->questionMode,
                    submittedAt: (string) (($question->created_at?->format('Y-m-d H:i:s')) ?: now()->format('Y-m-d H:i:s')),
                    questionModeLog: $this->questionMode,
                    questionId: (string) $question->id,
                    ticketId: $ticket?->id
                );
            } catch (\Throwable $th) {
                report($th);
            }

            return;
        }

        $responseMessage = trim((string) ($ticket?->response_message ?: $question->response_message ?: ''));
        if ($responseMessage === '') {
            return;
        }

        $phpMailerService->sendQuestionReplyNotification(
            customerName: (string) ($question->name ?: 'Customer'),
            customerEmail: (string) $question->email,
            ticketNo: (string) ($question->ticket_no ?: ($ticket?->ticket_number ?: '-')),
            questionMessage: (string) ($question->description ?: '-'),
            responseMessage: $responseMessage,
            questionMode: $this->questionMode,
            questionId: (string) $question->id,
            ticketId: $ticket?->id
        );
    }

    protected function resolveTicket(string $questionId): ?Ticket
    {
        if ($this->ticketId) {
            $ticket = Ticket::find($this->ticketId);
            if ($ticket) {
                return $ticket;
            }
        }

        return Ticket::where('question_mode', $this->questionMode)
            ->where('question_id', $questionId)
            ->first();
    }

    protected function resolveQuestionType($question): string
    {
        if ($this->questionMode === 'q2') {
            return (string) ($question->qtype ?: 'Website Inquiry');
        }

        $product = Product::where('id', $question->productid)->first();
        if (! $product) {
            return 'Product Inquiry';
        }

        $subject = (string) $product->title;
        if ($product->category) {
            $subject .= ' (' . $product->category . ')';
        }

        return $subject;
    }
}
