<?php

namespace App\Services\Admin;

use App\Models\LogEmailSender;
use Throwable;

class EmailLogService
{
    public function list(array $filters): array
    {
        $status = (string) ($filters['status'] ?? '');
        $dateFrom = (string) ($filters['date_from'] ?? '');
        $dateTo = (string) ($filters['date_to'] ?? '');
        $ticketNo = trim((string) ($filters['ticket_no'] ?? ''));

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

        return [
            'list' => $query->orderByDesc('created_at')->get(),
            'filters' => [
                'status' => $status,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'ticket_no' => $ticketNo,
            ],
        ];
    }

    public function findByEncryptedId(string $encryptedId): ?LogEmailSender
    {
        $id = $this->decryptId($encryptedId);
        if (! $id) {
            return null;
        }

        return LogEmailSender::find($id);
    }

    protected function decryptId(string $encryptedId): ?string
    {
        try {
            return (string) decrypt($encryptedId);
        } catch (Throwable) {
            return null;
        }
    }
}
