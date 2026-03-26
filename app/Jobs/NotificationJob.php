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
use Illuminate\Queue\SerializesModels;

class NotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected const EMAIL_COOLDOWN_SECONDS = 3600;
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

            if (! $this->hasSentNotification((string) $question->id, self::TEMPLATE_AUTO_RESPONSE_CUSTOMER)) {
                $customerCooldownDelay = $this->resolveRecipientEmailCooldownDelay((string) $question->email);
                if ($customerCooldownDelay > 0) {
                    $this->deferCurrentJob($customerCooldownDelay);
                    return;
                }

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

            if ($this->hasSentNotification((string) $question->id, self::TEMPLATE_NOTIFICATION_ADMIN)) {
                return;
            }

            $adminRecipientEmail = $phpMailerService->getAdminNotificationRecipientEmail();
            if ($adminRecipientEmail) {
                $adminCooldownDelay = $this->resolveRecipientEmailCooldownDelay($adminRecipientEmail);
                if ($adminCooldownDelay > 0) {
                    $this->deferCurrentJob($adminCooldownDelay);
                    return;
                }
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

        $replyCooldownDelay = $this->resolveRecipientEmailCooldownDelay((string) $question->email);
        if ($replyCooldownDelay > 0) {
            $this->deferCurrentJob($replyCooldownDelay);
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

    protected function resolveRecipientEmailCooldownDelay(string $email): int
    {
        $normalizedEmail = strtolower(trim($email));
        if ($normalizedEmail === '') {
            return 0;
        }

        $lastSentLog = LogEmailSender::query()
            ->whereRaw('LOWER(recipient_email) = ?', [$normalizedEmail])
            ->where('status', 'sent')
            ->orderByDesc('sent_at')
            ->orderByDesc('created_at')
            ->first(['sent_at', 'created_at']);

        if (! $lastSentLog) {
            return 0;
        }

        $lastSentAt = $lastSentLog->sent_at ?: $lastSentLog->created_at;
        if (! $lastSentAt) {
            return 0;
        }

        $elapsedSeconds = max(0, now()->getTimestamp() - $lastSentAt->getTimestamp());
        $remainingSeconds = self::EMAIL_COOLDOWN_SECONDS - $elapsedSeconds;

        return max(0, $remainingSeconds);
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

    protected function deferCurrentJob(int $delayInSeconds): void
    {
        if ($delayInSeconds > 0 && $this->job) {
            $this->release($delayInSeconds);
        }
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
