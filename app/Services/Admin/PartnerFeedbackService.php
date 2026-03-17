<?php

namespace App\Services\Admin;

use App\Models\JoinAsPartner;
use Illuminate\Support\Collection;
use Throwable;

class PartnerFeedbackService
{
    public function list(): Collection
    {
        return JoinAsPartner::orderBy('created_at', 'desc')
            ->orderBy('replied', 'asc')
            ->get();
    }

    public function findByEncryptedId(string $encryptedId): ?JoinAsPartner
    {
        $id = $this->decryptId($encryptedId);
        if (! $id) {
            return null;
        }

        return JoinAsPartner::find($id);
    }

    public function markAsReplied(string $encryptedId): bool
    {
        $id = $this->decryptId($encryptedId);
        if (! $id) {
            return false;
        }

        return JoinAsPartner::where('id', $id)->update([
            'replied' => 1,
        ]) > 0;
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
