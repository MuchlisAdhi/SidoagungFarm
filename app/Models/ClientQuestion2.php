<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientQuestion2 extends BaseModel
{
    use HasFactory;

    protected $table = 'clientquestion2';

    protected $guarded = [];

    protected $casts = [
        'replied' => 'boolean',
        'ticket_status' => TicketStatus::class,
    ];
}
