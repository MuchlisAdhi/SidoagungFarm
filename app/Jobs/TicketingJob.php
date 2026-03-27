<?php

namespace App\Jobs;

use App\Enums\TicketStatus;
use App\Models\ClientQuestion;
use App\Models\ClientQuestion2;
use App\Models\Product;
use App\Models\Ticket;
use App\Services\TicketNumberService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TicketingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $questionMode, public string $questionId)
    {
        $this->onQueue('tickets');
    }

    public function handle(TicketNumberService $ticketNumberService): void
    {
        if (! in_array($this->questionMode, ['q1', 'q2'])) {
            return;
        }

        $question = $this->questionMode === 'q1'
            ? ClientQuestion::find($this->questionId)
            : ClientQuestion2::find($this->questionId);

        if (! $question) {
            return;
        }

        $ticket = Ticket::where('question_mode', $this->questionMode)
            ->where('question_id', $question->id)
            ->first();

        $isNewTicket = false;

        DB::transaction(function () use ($question, $ticketNumberService, &$ticket, &$isNewTicket): void {
            if (! $ticket) {
                $ticketNo = (string) ($question->ticket_no ?: $ticketNumberService
                    ->generateForModel(Ticket::class, 'SAF', 'ticket_number'));

                $subject = $this->resolveSubject($question);

                $ticket = Ticket::create([
                    'ticket_number' => $ticketNo,
                    'question_mode' => $this->questionMode,
                    'question_id' => $question->id,
                    'subject' => $subject,
                    'message' => $question->description ?: '-',
                    'requester_name' => $question->name,
                    'requester_email' => $question->email,
                    'requester_phone' => $question->phone,
                    'status' => TicketStatus::New->value,
                    'priority' => 'normal',
                    'channel' => 'website',
                ]);

                $question->update([
                    'ticket_no' => $ticketNo,
                    'ticket_status' => TicketStatus::New->value,
                ]);

                $isNewTicket = true;
            } elseif (! $question->ticket_no) {
                $question->update([
                    'ticket_no' => $ticket->ticket_number,
                    'ticket_status' => TicketStatus::New->value,
                ]);
            }
        });

        if ($isNewTicket && $ticket) {
            try {
                NotificationJob::dispatch(
                    notificationType: 'ticket-created',
                    questionMode: $this->questionMode,
                    questionId: (string) $question->id,
                    ticketId: (string) $ticket->id
                )->onQueue('emails');
            } catch (\Throwable $th) {
                report($th);
            }
        }
    }

    protected function resolveSubject($question): string
    {
        if ($this->questionMode === 'q1') {
            $product = Product::where('id', $question->productid)->first();
            $subject = $product?->title ?: 'Product Inquiry';
            if ($product?->category) {
                $subject .= ' (' . $product->category . ')';
            }

            return $subject;
        }

        return $question->qtype ?: 'Website Inquiry';
    }
}
