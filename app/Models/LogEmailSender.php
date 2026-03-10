<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogEmailSender extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question_mode',
        'question_id',
        'ticket_id',
        'ticket_no',
        'recipient_email',
        'subject',
        'template',
        'body',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
