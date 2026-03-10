<?php

namespace App\Enums;

enum TicketStatus: string
{
    case New = 'new';
    case Open = 'open';
    case Responded = 'responded';
}
