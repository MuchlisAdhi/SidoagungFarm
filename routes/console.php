<?php

use App\Jobs\NotificationJob;
use App\Jobs\TicketingJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (): void {
    $now = now();
    $startTime = $now->copy()->setTime(6, 30);
    $endTime = $now->copy()->setTime(23, 0);

    if (! $now->between($startTime, $endTime)) {
        return;
    }

    $pendingTicketQ1 = DB::table('clientquestion as q')
        ->leftJoin('tickets as t', function ($join): void {
            $join->on('t.question_id', '=', 'q.id')
                ->where('t.question_mode', '=', 'q1');
        })
        ->where(function ($where): void {
            $where->whereNull('q.ticket_no')
                ->orWhere('q.ticket_no', '')
                ->orWhereNull('t.id');
        })
        ->orderBy('q.created_at')
        ->limit(20)
        ->pluck('q.id');

    foreach ($pendingTicketQ1 as $questionId) {
        TicketingJob::dispatch('q1', (string) $questionId)->onQueue('tickets');
    }

    $pendingTicketQ2 = DB::table('clientquestion2 as q')
        ->leftJoin('tickets as t', function ($join): void {
            $join->on('t.question_id', '=', 'q.id')
                ->where('t.question_mode', '=', 'q2');
        })
        ->where(function ($where): void {
            $where->whereNull('q.ticket_no')
                ->orWhere('q.ticket_no', '')
                ->orWhereNull('t.id');
        })
        ->orderBy('q.created_at')
        ->limit(20)
        ->pluck('q.id');

    foreach ($pendingTicketQ2 as $questionId) {
        TicketingJob::dispatch('q2', (string) $questionId)->onQueue('tickets');
    }

    $pendingCreateNotifQ1 = DB::table('clientquestion as q')
        ->leftJoin('tickets as t', function ($join): void {
            $join->on('t.question_id', '=', 'q.id')
                ->where('t.question_mode', '=', 'q1');
        })
        ->leftJoin('log_email_senders as l', function ($join): void {
            $join->on('l.question_id', '=', 'q.id')
                ->where('l.question_mode', '=', 'q1')
                ->where('l.template', '=', 'auto-response-customer')
                ->where('l.status', '=', 'sent');
        })
        ->whereNotNull('q.ticket_no')
        ->where('q.ticket_no', '!=', '')
        ->whereNull('l.id')
        ->orderBy('q.created_at')
        ->limit(20)
        ->get(['q.id', 't.id as ticket_id']);

    foreach ($pendingCreateNotifQ1 as $row) {
        NotificationJob::dispatch(
            'ticket-created',
            'q1',
            (string) $row->id,
            $row->ticket_id ? (string) $row->ticket_id : null
        )->onQueue('emails');
    }

    $pendingCreateNotifQ2 = DB::table('clientquestion2 as q')
        ->leftJoin('tickets as t', function ($join): void {
            $join->on('t.question_id', '=', 'q.id')
                ->where('t.question_mode', '=', 'q2');
        })
        ->leftJoin('log_email_senders as l', function ($join): void {
            $join->on('l.question_id', '=', 'q.id')
                ->where('l.question_mode', '=', 'q2')
                ->where('l.template', '=', 'auto-response-customer')
                ->where('l.status', '=', 'sent');
        })
        ->whereNotNull('q.ticket_no')
        ->where('q.ticket_no', '!=', '')
        ->whereNull('l.id')
        ->orderBy('q.created_at')
        ->limit(20)
        ->get(['q.id', 't.id as ticket_id']);

    foreach ($pendingCreateNotifQ2 as $row) {
        NotificationJob::dispatch(
            'ticket-created',
            'q2',
            (string) $row->id,
            $row->ticket_id ? (string) $row->ticket_id : null
        )->onQueue('emails');
    }

    $pendingReplyNotifQ1 = DB::table('clientquestion as q')
        ->leftJoin('tickets as t', function ($join): void {
            $join->on('t.question_id', '=', 'q.id')
                ->where('t.question_mode', '=', 'q1');
        })
        ->leftJoin('log_email_senders as l', function ($join): void {
            $join->on('l.question_id', '=', 'q.id')
                ->where('l.question_mode', '=', 'q1')
                ->where('l.template', '=', 'reply-customer')
                ->where('l.status', '=', 'sent');
        })
        ->where('q.replied', 1)
        ->whereNotNull('t.response_message')
        ->where('t.response_message', '!=', '')
        ->whereNull('l.id')
        ->orderBy('q.created_at')
        ->limit(20)
        ->get(['q.id', 't.id as ticket_id']);

    foreach ($pendingReplyNotifQ1 as $row) {
        NotificationJob::dispatch(
            'ticket-responded',
            'q1',
            (string) $row->id,
            $row->ticket_id ? (string) $row->ticket_id : null
        )->onQueue('emails');
    }

    $pendingReplyNotifQ2 = DB::table('clientquestion2 as q')
        ->leftJoin('tickets as t', function ($join): void {
            $join->on('t.question_id', '=', 'q.id')
                ->where('t.question_mode', '=', 'q2');
        })
        ->leftJoin('log_email_senders as l', function ($join): void {
            $join->on('l.question_id', '=', 'q.id')
                ->where('l.question_mode', '=', 'q2')
                ->where('l.template', '=', 'reply-customer')
                ->where('l.status', '=', 'sent');
        })
        ->where('q.replied', 1)
        ->whereNotNull('t.response_message')
        ->where('t.response_message', '!=', '')
        ->whereNull('l.id')
        ->orderBy('q.created_at')
        ->limit(20)
        ->get(['q.id', 't.id as ticket_id']);

    foreach ($pendingReplyNotifQ2 as $row) {
        NotificationJob::dispatch(
            'ticket-responded',
            'q2',
            (string) $row->id,
            $row->ticket_id ? (string) $row->ticket_id : null
        )->onQueue('emails');
    }
})
    ->name('ticketing-and-notification-dispatcher')
    ->withoutOverlapping()
    ->everyMinute();

if ((string) config('queue.default') === 'database') {
    Schedule::command('queue:work database --queue=tickets,emails,default --sleep=1 --tries=3 --timeout=120 --stop-when-empty')
        ->name('database-queue-worker')
        ->withoutOverlapping()
        ->everyMinute();
}
