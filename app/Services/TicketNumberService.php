<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class TicketNumberService
{
    /**
     * Generate ticket number with daily sequence.
     *
     * Example: SAF20260309-0001
     *
     * @param  class-string<Model>  $modelClass
     */
    public function generateForModel(string $modelClass, string $prefix = 'SAF', string $column = 'ticket_no'): string
    {
        $basePrefix = $prefix . now()->format('Ymd') . '-';

        $lastTicket = $modelClass::where($column, 'like', $basePrefix . '%')
            ->orderByDesc($column)
            ->value($column);

        $nextSequence = 1;
        if ($lastTicket) {
            $nextSequence = ((int) substr($lastTicket, strlen($basePrefix))) + 1;
        }

        return $basePrefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);
    }
}
