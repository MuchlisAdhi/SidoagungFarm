<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationAccess;
use App\Models\User;
use App\Services\Contracts\INavigationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class NavigationAccessController extends Controller
{
    private INavigationService $_navigationService;

    private array $actions = ['read', 'write', 'update', 'delete', 'export'];

    public function __construct(INavigationService $navigationService)
    {
        $this->_navigationService = $navigationService;
    }

    public function index()
    {
        $this->abortWhenUnauthorized('navigation-access', 'read');
        $this->_navigationService->BootstrapNavigationAccess();

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        $descriptions = NavigationAccess::query()
            ->pluck('description', 'role_id')
            ->toArray();

        return view('admin.navigation-access', [
            'actions' => $this->actions,
            'navigationRows' => $this->buildNavigationRows($this->_navigationService->GetDefaultNavigation()),
            'existingAccesses' => $roles->map(function (Role $role) use ($descriptions) {
                return [
                    'encrypted_id' => encrypt($role->id),
                    'name' => $role->name,
                    'description' => $descriptions[$role->id] ?? '',
                ];
            }),
        ]);
    }

    public function get(string $id)
    {
        $this->abortWhenUnauthorized('navigation-access', 'read');

        try {
            $roleId = decrypt($id);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 0,
                'msg' => 'Data access tidak valid.',
                'data' => [],
            ]);
        }

        $role = Role::query()->where('id', $roleId)->where('guard_name', 'web')->first();
        if (! $role) {
            return response()->json([
                'code' => 0,
                'msg' => 'Navigation access tidak ditemukan.',
                'data' => [],
            ]);
        }

        $description = NavigationAccess::query()->where('role_id', $role->id)->value('description');
        return response()->json([
            'code' => 200,
            'msg' => '',
            'data' => [
                'id' => encrypt($role->id),
                'name' => $role->name,
                'description' => $description,
                'permissions' => $role->permissions()->pluck('name')->values(),
            ],
        ]);
    }

    public function save(Request $request)
    {
        $isUpdate = (string) $request->input('access_id', '') !== '';
        $this->abortWhenUnauthorized('navigation-access', $isUpdate ? 'update' : 'write');

        $roleId = null;
        if ($isUpdate) {
            try {
                $roleId = decrypt((string) $request->input('access_id'));
            } catch (\Throwable $th) {
                return response()->json([
                    'code' => 0,
                    'msg' => 'Data access tidak valid.',
                    'data' => [],
                ]);
            }
        }

        $validator = Validator::make($request->all(), [
            'access_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique((new Role())->getTable(), 'name')
                    ->where(fn ($q) => $q->where('guard_name', 'web'))
                    ->ignore($roleId),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 0,
                'msg' => $validator->errors()->first(),
                'data' => [],
            ]);
        }

        $validated = $validator->validated();
        $role = $roleId
            ? Role::query()->where('id', $roleId)->where('guard_name', 'web')->first()
            : new Role(['guard_name' => 'web']);

        if ($roleId && ! $role) {
            return response()->json([
                'code' => 0,
                'msg' => 'Navigation access tidak ditemukan.',
                'data' => [],
            ]);
        }

        $role->name = (string) $validated['access_name'];
        $role->guard_name = 'web';
        $role->save();

        NavigationAccess::query()->updateOrCreate(
            ['role_id' => $role->id],
            ['description' => (string) ($validated['description'] ?? '')]
        );

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $this->sanitizePermissionList($validated['permissions'] ?? []))
            ->get();

        $role->syncPermissions($permissions);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'code' => 200,
            'msg' => 'Navigation access berhasil disimpan.',
            'data' => ['id' => encrypt($role->id)],
        ]);
    }

    public function delete(Request $request)
    {
        $this->abortWhenUnauthorized('navigation-access', 'delete');

        $validator = Validator::make($request->all(), [
            'access_id' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 0,
                'msg' => $validator->errors()->first(),
                'data' => [],
            ]);
        }

        try {
            $roleId = decrypt((string) $request->input('access_id'));
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 0,
                'msg' => 'Data access tidak valid.',
                'data' => [],
            ]);
        }

        $role = Role::query()->where('id', $roleId)->where('guard_name', 'web')->first();
        if (! $role) {
            return response()->json([
                'code' => 0,
                'msg' => 'Navigation access tidak ditemukan.',
                'data' => [],
            ]);
        }

        if (strtolower($role->name) === 'admin access') {
            return response()->json([
                'code' => 0,
                'msg' => 'Admin Access tidak dapat dihapus.',
                'data' => [],
            ]);
        }

        $usedByUser = User::role($role->name)->exists();
        if ($usedByUser) {
            return response()->json([
                'code' => 0,
                'msg' => 'Navigation access masih digunakan oleh user.',
                'data' => [],
            ]);
        }

        NavigationAccess::query()->where('role_id', $role->id)->delete();
        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return response()->json([
            'code' => 200,
            'msg' => 'Navigation access berhasil dihapus.',
            'data' => [],
        ]);
    }

    private function abortWhenUnauthorized(string $navigationKey, string $action = 'read'): void
    {
        if (! $this->_navigationService->CheckAccessNavigation(auth()->user(), $navigationKey, $action)) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }
    }

    private function sanitizePermissionList(array $permissions): array
    {
        return array_values(array_unique(array_filter(array_map(function ($permission) {
            $permission = strtolower(trim((string) $permission));
            return str_starts_with($permission, 'nav.') ? $permission : null;
        }, $permissions))));
    }

    private function buildNavigationRows(array $menus, int $level = 0): array
    {
        $rows = [];
        foreach ($menus as $menu) {
            $rows[] = [
                'key' => (string) $menu['key'],
                'title' => (string) $menu['title'],
                'level' => $level,
            ];

            foreach (($menu['childs'] ?? []) as $child) {
                $rows[] = [
                    'key' => (string) $child['key'],
                    'title' => (string) $child['title'],
                    'level' => $level + 1,
                ];
            }
        }

        return $rows;
    }
}

