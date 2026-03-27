<?php

namespace App\Jobs;

use App\Models\ClientQuestion;
use App\Models\ClientQuestion2;
use App\Models\LogEmailSender;
use App\Models\Product;
use App\Models\Ticket;
use App\Services\PhpMailerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

class NotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected const EMAIL_DELAY_SECONDS = 3600;
    protected const TYPE_TICKET_CREATED = 'ticket-created';
    protected const TYPE_TICKET_CREATED_ADMIN = 'ticket-created-admin';
    protected const TYPE_TICKET_RESPONDED = 'ticket-responded';
    protected const TEMPLATE_AUTO_RESPONSE_CUSTOMER = 'auto-response-customer';
    protected const TEMPLATE_REPLY_CUSTOMER = 'reply-customer';
    protected const TEMPLATE_NOTIFICATION_ADMIN = 'notification-admin';

    public function __construct(
        public string $notificationType,
        public string $questionMode,
        public string $questionId,
        public ?string $ticketId = null
    ) {
        $this->onQueue('emails');
    }

    public function middleware(): array
    {
        return [new RateLimited('notification-emails-global')];
    }

    public function handle(PhpMailerService $phpMailerService): void
    {
        if (! in_array($this->notificationType, [
            self::TYPE_TICKET_CREATED,
            self::TYPE_TICKET_CREATED_ADMIN,
            self::TYPE_TICKET_RESPONDED,
        ], true)) {
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
        $questionType = $this->resolveQuestionType($question);
        $ticketNo = (string) ($question->ticket_no ?: ($ticket?->ticket_number ?: '-'));

        if ($this->notificationType === self::TYPE_TICKET_CREATED) {
            if (! $this->hasSentNotification((string) $question->id, self::TEMPLATE_AUTO_RESPONSE_CUSTOMER)) {
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
            }

            if (! $this->hasSentNotification((string) $question->id, self::TEMPLATE_NOTIFICATION_ADMIN)) {
                self::dispatch(
                    notificationType: self::TYPE_TICKET_CREATED_ADMIN,
                    questionMode: $this->questionMode,
                    questionId: (string) $question->id,
                    ticketId: $ticket?->id
                )
                    ->onQueue('emails')
                    ->delay(now()->addSeconds(self::EMAIL_DELAY_SECONDS));
            }

            return;
        }

        if ($this->notificationType === self::TYPE_TICKET_CREATED_ADMIN) {
            if ($this->hasSentNotification((string) $question->id, self::TEMPLATE_NOTIFICATION_ADMIN)) {
                return;
            }

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

        $responseMessage = trim((string) ($ticket?->response_message ?: ''));
        if ($responseMessage === '') {
            return;
        }

        if ($this->hasSentNotification((string) $question->id, self::TEMPLATE_REPLY_CUSTOMER)) {
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

    protected function hasSentNotification(string $questionId, string $template): bool
    {
        return LogEmailSender::query()
            ->where('question_mode', $this->questionMode)
            ->where('question_id', $questionId)
            ->where('template', $template)
            ->where('status', 'sent')
            ->exists();
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
