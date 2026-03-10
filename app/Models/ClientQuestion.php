<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientQuestion extends BaseModel
{
    use HasFactory;

    protected $table = 'clientquestion';

    protected $guarded = [];

    protected $casts = [
        'replied' => 'boolean',
        'responded_at' => 'datetime',
        'ticket_status' => TicketStatus::class,
    ];
}
