<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogEmailSender;

class EmailLogController extends Controller
{
    public function list()
    {
        $status = (string) request()->query('status', '');
        $dateFrom = (string) request()->query('date_from', '');
        $dateTo = (string) request()->query('date_to', '');
        $ticketNo = trim((string) request()->query('ticket_no', ''));

        $query = LogEmailSender::query();
        if (in_array($status, ['queued', 'sent', 'failed'], true)) {
            $query->where('status', $status);
        }
        if ($ticketNo !== '') {
            $query->where('ticket_no', 'like', '%' . $ticketNo . '%');
        }
        if ($dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $list = $query->orderByDesc('created_at')->get();
        $filters = [
            'status' => $status,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'ticket_no' => $ticketNo,
        ];

        return view('admin.email-log.list', compact('list', 'filters'));
    }

    public function show($id)
    {
        try {
            $id = decrypt($id);
            $row = LogEmailSender::where('id', $id)->firstOrFail();
        } catch (\Throwable $th) {
            return redirect('/wongelek/email-log')->with('error', 'Log email tidak ditemukan.');
        }

        return view('admin.email-log.detail', compact('row'));
    }
}
