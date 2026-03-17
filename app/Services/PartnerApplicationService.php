<?php

namespace App\Services;

use App\Models\JoinAsPartner;

class PartnerApplicationService
{
    public function submit(array $payload): JoinAsPartner
    {
        return JoinAsPartner::create($payload);
    }
}
