<?php

namespace App\Services\Contracts;

use App\Models\User;

interface INavigationService
{
    public function GetDefaultNavigation(): array;

    public function GetAccessNavigation(?User $user = null): array;

    public function CheckAccessNavigation(?User $user, string $navigationKey, string $action = 'read'): bool;

    public function BootstrapNavigationAccess(): void;
}
