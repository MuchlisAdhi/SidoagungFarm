<?php

namespace App\Observers;

use App\Jobs\TicketingJob;
use App\Models\ClientQuestion2;

class ClientQuestion2Observer
{
    public function created(ClientQuestion2 $clientQuestion): void
    {
        TicketingJob::dispatchSync('q2', (string) $clientQuestion->id);
    }
}
