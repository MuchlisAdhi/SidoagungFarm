<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'question_mode',
        'question_id',
        'subject',
        'message',
        'requester_name',
        'requester_email',
        'requester_phone',
        'status',
        'priority',
        'channel',
        'response_message',
        'responded_at',
    ];

    protected $casts = [
        'status' => TicketStatus::class,
        'responded_at' => 'datetime',
    ];
}
