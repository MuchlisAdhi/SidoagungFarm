<?php

namespace App\Services;

use App\Models\NavigationAccess;
use App\Models\User;
use App\Services\Contracts\INavigationService;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class NavigationService implements INavigationService
{
    private const ACTIONS = ['read', 'write', 'update', 'delete', 'export'];

    private static bool $bootstrapped = false;

    public function GetDefaultNavigation(): array
    {
        $base = '/admin';

        return [
            [
                'key' => 'home',
                'url' => '',
                'title' => 'Home',
                'icon' => 'fa fa-home',
                'childs' => [
                    ['key' => 'home.banner', 'url' => url($base . '/home/banner'), 'title' => 'Banners'],
                    ['key' => 'home.banner-menu', 'url' => url($base . '/home/banner-menu'), 'title' => 'Banner Menu'],
                ],
            ],
            [
                'key' => 'product',
                'url' => url($base . '/product'),
                'title' => 'Produk',
                'icon' => 'fa fa-angle-double-right',
                'childs' => [],
            ],
            [
                'key' => 'csr',
                'url' => '',
                'title' => 'CSR',
                'icon' => 'fa fa-building-o',
                'childs' => [
                    ['key' => 'csr.env', 'url' => url($base . '/csr/env'), 'title' => 'Pendidikan'],
                    ['key' => 'csr.safety', 'url' => url($base . '/csr/safety'), 'title' => 'Kesehatan & Keselamatan'],
                    ['key' => 'csr.sosial', 'url' => url($base . '/csr/sosial'), 'title' => 'Sosial'],
                ],
            ],
            [
                'key' => 'news',
                'url' => url($base . '/news'),
                'title' => 'Berita',
                'icon' => 'fa fa-angle-double-right',
                'childs' => [],
            ],
            [
                'key' => 'testimoni',
                'url' => url($base . '/testimoni'),
                'title' => 'Testimoni',
                'icon' => 'fa fa-angle-double-right',
                'childs' => [],
            ],
            [
                'key' => 'feedback',
                'url' => '',
                'title' => 'Feedback',
                'icon' => 'fa fa-comments-o',
                'childs' => [
                    ['key' => 'feedback.karir', 'url' => url($base . '/feedback/karir'), 'title' => 'Karir'],
                    ['key' => 'feedback.pertanyaan', 'url' => url($base . '/feedback/pertanyaan'), 'title' => 'Pertanyaan'],
                    ['key' => 'feedback.mitra', 'url' => url($base . '/feedback/mitra'), 'title' => 'Menjadi Mitra'],
                    ['key' => 'feedback.ticket', 'url' => url($base . '/ticket'), 'title' => 'Tickets'],
                ],
            ],
            [
                'key' => 'karir',
                'url' => url($base . '/karir'),
                'title' => 'Karir',
                'icon' => 'fa fa-angle-double-right',
                'childs' => [],
            ],
            [
                'key' => 'users',
                'url' => url($base . '/users'),
                'title' => 'Users',
                'icon' => 'fa fa-angle-double-right',
                'childs' => [],
            ],
            [
                'key' => 'config',
                'url' => '',
                'title' => 'Config',
                'icon' => 'fa fa-cog',
                'childs' => [
                    ['key' => 'navigation-access', 'url' => url($base . '/navigation-access'), 'title' => 'Navigation Access'],
                    ['key' => 'email-log', 'url' => url($base . '/email-log'), 'title' => 'Email Logs'],
                    ['key' => 'email-config', 'url' => url($base . '/email-config'), 'title' => 'Email Config'],
                ],
            ],
        ];
    }

    public function GetAccessNavigation(?User $user = null): array
    {
        $this->BootstrapNavigationAccess();

        $menus = $this->GetDefaultNavigation();
        if (! $user) {
            return [];
        }

        if ($this->isSuperAdmin($user)) {
            return $menus;
        }

        $allowedMenus = [];
        foreach ($menus as $menu) {
            $children = $menu['childs'] ?? [];

            if (count($children)) {
                $filteredChildren = [];
                foreach ($children as $child) {
                    if ($this->CheckAccessNavigation($user, $child['key'], 'read')) {
                        $filteredChildren[] = $child;
                    }
                }

                if (count($filteredChildren)) {
                    $menu['childs'] = $filteredChildren;
                    $allowedMenus[] = $menu;
                }
            } else {
                if ($this->CheckAccessNavigation($user, $menu['key'], 'read')) {
                    $allowedMenus[] = $menu;
                }
            }
        }

        return $allowedMenus;
    }

    public function CheckAccessNavigation(?User $user, string $navigationKey, string $action = 'read'): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->isSuperAdmin($user)) {
            return true;
        }

        if (! $this->hasPermissionTables()) {
            return false;
        }

        $permissionName = $this->buildPermissionName($navigationKey, $action);
        return $user->can($permissionName);
    }

    public function BootstrapNavigationAccess(): void
    {
        if (self::$bootstrapped || ! $this->hasPermissionTables()) {
            return;
        }

        try {
            $permissionNames = [];
            foreach ($this->flattenNavigationKeys($this->GetDefaultNavigation()) as $navigationKey) {
                foreach (self::ACTIONS as $action) {
                    $permissionNames[] = $this->buildPermissionName($navigationKey, $action);
                }
            }

            $permissions = [];
            foreach ($permissionNames as $permissionName) {
                $permissions[] = Permission::findOrCreate($permissionName, 'web');
            }
            $permissions = collect($permissions)
                ->unique(fn (Permission $permission): int|string => $permission->getKey())
                ->values();

            $adminRole = Role::findOrCreate('Admin Access', 'web');
            $expectedPermissionIds = $permissions->pluck('id')
                ->map(fn ($id): int => (int) $id)
                ->sort()
                ->values()
                ->all();
            $currentPermissionIds = $adminRole->permissions()
                ->pluck('permissions.id')
                ->map(fn ($id): int => (int) $id)
                ->sort()
                ->values()
                ->all();
            if ($expectedPermissionIds !== $currentPermissionIds) {
                $adminRole->syncPermissions($expectedPermissionIds);
            }

            if (Schema::hasTable('users')) {
                $adminUser = User::query()
                    ->whereRaw('LOWER(email) = ?', ['admin@admin.com'])
                    ->first();

                if ($adminUser && ! $adminUser->hasRole($adminRole->name)) {
                    $adminUser->assignRole($adminRole->name);
                }
            }

            if (Schema::hasTable('navigation_accesses')) {
                NavigationAccess::query()->updateOrCreate(
                    ['role_id' => $adminRole->id],
                    ['description' => 'Default full access for administrator']
                );
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();
            self::$bootstrapped = true;
        } catch (\Throwable $th) {
            report($th);
        }
    }

    private function flattenNavigationKeys(array $menus): array
    {
        $keys = [];
        foreach ($menus as $menu) {
            $key = (string) ($menu['key'] ?? '');
            if ($key !== '') {
                $keys[] = $key;
            }

            foreach (($menu['childs'] ?? []) as $child) {
                $childKey = (string) ($child['key'] ?? '');
                if ($childKey !== '') {
                    $keys[] = $childKey;
                }
            }
        }

        return array_values(array_unique($keys));
    }

    private function buildPermissionName(string $navigationKey, string $action): string
    {
        $safeAction = in_array($action, self::ACTIONS, true) ? $action : 'read';
        return 'nav.' . strtolower(trim($navigationKey)) . '.' . $safeAction;
    }

    private function hasPermissionTables(): bool
    {
        return Schema::hasTable('permissions')
            && Schema::hasTable('roles')
            && Schema::hasTable('model_has_roles')
            && Schema::hasTable('model_has_permissions')
            && Schema::hasTable('role_has_permissions');
    }

    private function isSuperAdmin(User $user): bool
    {
        return strtolower((string) $user->email) === 'admin@admin.com';
    }
}

