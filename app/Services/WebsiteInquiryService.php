<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\ClientQuestion;
use App\Models\ClientQuestion2;

class WebsiteInquiryService
{
    public function submitProductQuestion(array $payload): ClientQuestion
    {
        $question = ClientQuestion::create([
            ...$payload,
            'ticket_status' => TicketStatus::New->value,
        ]);

        $question->refresh();

        return $question;
    }

    public function submitGeneralQuestion(array $payload): ClientQuestion2
    {
        $question = ClientQuestion2::create([
            ...$payload,
            'ticket_status' => TicketStatus::New->value,
        ]);

        $question->refresh();

        return $question;
    }
}
