<?php

namespace App\Observers;

use App\Jobs\TicketingJob;
use App\Models\ClientQuestion;

class ClientQuestionObserver
{
    public function created(ClientQuestion $clientQuestion): void
    {
        TicketingJob::dispatchSync('q1', (string) $clientQuestion->id);
    }
}
