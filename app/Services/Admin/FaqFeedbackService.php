<?php

namespace App\Services\Admin;

use App\Enums\QuestionType;
use App\Enums\TicketStatus;
use App\Jobs\NotificationJob;
use App\Jobs\TicketingJob;
use App\Models\ClientQuestion;
use App\Models\ClientQuestion2;
use App\Models\Product;
use App\Models\Ticket;
use App\Services\TicketNumberService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class FaqFeedbackService
{
    public function __construct(
        protected TicketNumberService $ticketNumberService
    ) {}

    public function getFaqList(array $allowedQuestionTypes = []): Collection
    {
        $allowedQuestionTypes = QuestionType::normalize($allowedQuestionTypes);

        $q1 = ClientQuestion::select(
            DB::raw('clientquestion.id'),
            DB::raw('clientquestion.name'),
            DB::raw('clientquestion.email'),
            DB::raw('clientquestion.phone'),
            DB::raw('clientquestion.ticket_no'),
            DB::raw('clientquestion.replied'),
            DB::raw('clientquestion.ticket_status'),
            DB::raw('clientquestion.description'),
            DB::raw('clientquestion.created_at'),
            DB::raw('COALESCE(product.title, tq1.subject) as title'),
            DB::raw('product.category'),
            DB::raw("'q1' as mode")
        )->leftJoin('product', function ($join) {
            $join->on('clientquestion.productid', '=', 'product.id');
        })->leftJoin('tickets as tq1', function ($join) {
            $join->on('tq1.question_id', '=', 'clientquestion.id')
                ->where('tq1.question_mode', '=', 'q1');
        });

        if (! QuestionType::isAll($allowedQuestionTypes)) {
            if (! in_array(QuestionType::Produk->value, $allowedQuestionTypes, true)) {
                $q1->whereRaw('1 = 0');
            }
        }

        $q2 = ClientQuestion2::select(
            'id',
            'name',
            'email',
            'phone',
            'ticket_no',
            'replied',
            'ticket_status',
            'description',
            'created_at',
            DB::raw("'' as title"),
            DB::raw('qtype as category'),
            DB::raw("'q2' as mode")
        );

        if (! QuestionType::isAll($allowedQuestionTypes)) {
            $q2->whereIn('qtype', QuestionType::expandForFiltering($allowedQuestionTypes));
        }

        return $q2->union($q1)
            ->orderBy('created_at', 'desc')
            ->orderBy('replied', 'asc')
            ->get();
    }

    public function getFaqDetail(string $encryptedId, string $mode, array $allowedQuestionTypes = []): object
    {
        $id = $this->decryptId($encryptedId);
        if (! $id) {
            throw new InvalidArgumentException('Request tidak valid.');
        }

        $row = $this->findFaqRow($id, $mode);
        if (! $row || ! $this->canAccessFaqRow($row, $mode, $allowedQuestionTypes)) {
            throw new ModelNotFoundException('Data tidak ditemukan.');
        }

        $status = $this->resolveTicketStatus($row->ticket_status ?? null);
        if ($status === TicketStatus::New->value) {
            $ticket = $this->ensureTicketExists($mode, (string) $row->id);
            if (($ticket->status?->value ?? '') === TicketStatus::New->value) {
                $ticket->update([
                    'status' => TicketStatus::Open->value,
                ]);
            }

            $row = $this->findFaqRow($id, $mode);
            if (! $row || ! $this->canAccessFaqRow($row, $mode, $allowedQuestionTypes)) {
                throw new ModelNotFoundException('Data tidak ditemukan.');
            }
        }

        return $row;
    }

    public function reply(
        string $encryptedId,
        string $mode,
        string $response,
        array $allowedQuestionTypes = []
    ): string
    {
        $id = $this->decryptId($encryptedId);
        if (! $id) {
            throw new InvalidArgumentException('Request tidak valid.');
        }

        $questionModel = $mode === 'q1' ? ClientQuestion::class : ClientQuestion2::class;
        $question = $questionModel::find($id);
        if (! $question) {
            throw new ModelNotFoundException('Data tidak ditemukan.');
        }

        if (! $this->hasQuestionTypeAccess(
            $this->resolveQuestionType($mode, $question),
            $allowedQuestionTypes
        )) {
            throw new ModelNotFoundException('Data tidak ditemukan.');
        }

        $ticket = $this->ensureTicketExists($mode, (string) $question->id, $question);

        $payload = [
            'status' => TicketStatus::Responded->value,
            'response_message' => $response,
            'responded_at' => now(),
        ];

        if (! $ticket->ticket_number) {
            $payload['ticket_number'] = $this->ticketNumberService
                ->generateForModel(Ticket::class, 'SAF', 'ticket_number');
        }

        $ticket->update($payload);
        $ticket->refresh();

        $warning = '';
        if ($question->email) {
            try {
                NotificationJob::dispatch(
                    notificationType: 'ticket-responded',
                    questionMode: $mode,
                    questionId: (string) $question->id,
                    ticketId: (string) $ticket->id
                );
            } catch (Throwable $th) {
                report($th);
                $warning = ' Jawaban tersimpan, tetapi email gagal dikirim.';
            }
        }

        return $warning;
    }

    protected function findFaqRow(string $id, string $mode): ?object
    {
        if ($mode === 'q2') {
            return ClientQuestion2::select(
                'id',
                'name',
                'email',
                'phone',
                'ticket_no',
                'replied',
                'ticket_status',
                'description',
                DB::raw("'' as title"),
                DB::raw('qtype as category')
            )->where('id', $id)->first();
        }

        return ClientQuestion::select(
            DB::raw('clientquestion.id'),
            DB::raw('clientquestion.name'),
            DB::raw('clientquestion.email'),
            DB::raw('clientquestion.phone'),
            DB::raw('clientquestion.ticket_no'),
            DB::raw('clientquestion.replied'),
            DB::raw('clientquestion.ticket_status'),
            DB::raw('clientquestion.description'),
            DB::raw('COALESCE(product.title, tq1.subject) as title'),
            DB::raw('product.category')
        )->leftJoin('product', function ($join) {
            $join->on('clientquestion.productid', '=', 'product.id');
        })->leftJoin('tickets as tq1', function ($join) {
            $join->on('tq1.question_id', '=', 'clientquestion.id')
                ->where('tq1.question_mode', '=', 'q1');
        })->where('clientquestion.id', $id)->first();
    }

    protected function ensureTicketExists(string $mode, string $questionId, $question = null): Ticket
    {
        TicketingJob::dispatchSync($mode, $questionId);

        $ticket = Ticket::where('question_mode', $mode)
            ->where('question_id', $questionId)
            ->first();

        if ($ticket) {
            return $ticket;
        }

        if (! $question) {
            $questionModel = $mode === 'q1' ? ClientQuestion::class : ClientQuestion2::class;
            $question = $questionModel::find($questionId);
        }

        if (! $question) {
            throw new ModelNotFoundException('Data tidak ditemukan.');
        }

        $ticketNo = (string) ($question->ticket_no ?: $this->ticketNumberService
            ->generateForModel(Ticket::class, 'SAF', 'ticket_number'));

        return Ticket::create([
            'ticket_number' => $ticketNo,
            'question_mode' => $mode,
            'question_id' => $question->id,
            'subject' => $this->resolveSubject($mode, $question),
            'message' => $question->description ?: '-',
            'requester_name' => $question->name,
            'requester_email' => $question->email,
            'requester_phone' => $question->phone,
            'status' => TicketStatus::New->value,
            'priority' => 'normal',
            'channel' => 'website',
        ]);
    }

    protected function resolveSubject(string $mode, $question): string
    {
        if ($mode === 'q2') {
            return (string) ($question->qtype ?: 'Website Inquiry');
        }

        $product = Product::where('id', $question->productid)->first();
        $subject = $product?->title ?: 'Product Inquiry';
        if ($product?->category) {
            $subject .= ' (' . $product->category . ')';
        }

        return $subject;
    }

    protected function decryptId(string $encryptedId): ?string
    {
        try {
            return (string) decrypt($encryptedId);
        } catch (Throwable) {
            return null;
        }
    }

    protected function canAccessFaqRow(object $row, string $mode, array $allowedQuestionTypes): bool
    {
        if ($mode === 'q1') {
            return $this->hasQuestionTypeAccess(QuestionType::Produk->value, $allowedQuestionTypes);
        }

        return $this->hasQuestionTypeAccess((string) ($row->category ?? ''), $allowedQuestionTypes);
    }

    protected function resolveQuestionType(string $mode, object $question): ?string
    {
        if ($mode === 'q1') {
            return QuestionType::Produk->value;
        }

        $questionType = trim((string) ($question->qtype ?? ''));
        return $questionType !== '' ? $questionType : null;
    }

    protected function hasQuestionTypeAccess(?string $questionType, array $allowedQuestionTypes): bool
    {
        $allowedQuestionTypes = QuestionType::normalize($allowedQuestionTypes);
        if (QuestionType::isAll($allowedQuestionTypes)) {
            return true;
        }

        if (! $questionType) {
            return false;
        }

        return QuestionType::hasAccess($questionType, $allowedQuestionTypes);
    }

    protected function resolveTicketStatus(mixed $ticketStatus): string
    {
        if ($ticketStatus instanceof TicketStatus) {
            return $ticketStatus->value;
        }

        $status = trim((string) $ticketStatus);
        return $status !== '' ? $status : TicketStatus::New->value;
    }
}
