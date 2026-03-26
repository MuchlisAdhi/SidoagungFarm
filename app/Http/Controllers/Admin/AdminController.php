<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveUserRequest;
use App\Models\NavigationAccess;
use App\Models\User;
use App\Services\Contracts\INavigationService;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    private INavigationService $_navigationService;

    public function __construct(INavigationService $navigationService)
    {
        $this->_navigationService = $navigationService;
    }

    public function main(){

        return view("admin.main");
    }

    public function userList()
    {
        $this->abortWhenUnauthorized('users', 'read');
        $this->_navigationService->BootstrapNavigationAccess();

        return view("admin.users", [
            'users' => User::query()->with('roles')->get(),
            'navigationAccesses' => NavigationAccess::query()
                ->with('role')
                ->whereHas('role', function ($query) {
                    $query->where('guard_name', 'web');
                })
                ->get()
                ->sortBy(fn (NavigationAccess $access) => strtolower((string) optional($access->role)->name))
                ->values()
                ->map(fn (NavigationAccess $access) => [
                    'id' => encrypt($access->role_id),
                    'name' => optional($access->role)->name,
                ]),
        ]);
    }

    public function getOne($id)
    {
        $this->abortWhenUnauthorized('users', 'read');

        $id = decrypt($id);
        $user = User::query()->with('roles')->where('id', $id)->first();
        return response()->json([
            'code' => $code = $user ? 200 : 0,
            'msg'   => !$code ? "User tidak terdaftar." : "",
            'data'  => [
                'name'  => $user?->name,
                'email' => $user?->email,
                'navigation_access' => $user?->roles?->first() ? encrypt($user->roles->first()->id) : '',
            ]
        ]);
    }

    public function remove($id)
    {
        $this->abortWhenUnauthorized('users', 'delete');

        $id = decrypt($id);
        $user = User::where('id', $id)->first();

        if(!$user)
        {
            session()->flash("error", "User tidak terdaftar.");
        }else{
            if(strtolower($user->email) == "admin@sidoagungfarm.com")
            {
                session()->flash("error", "User tidak dapat dihapus.");
            }else{
                $user->delete();
                session()->flash("success", "User berhasil di hapus.");
            }
        }
        return response()->json([
            'code' => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }

    public function save(SaveUserRequest $request)
    {
        $validated = $request->validated();

        $isUpdate = ! empty($validated['id']);
        $this->abortWhenUnauthorized('users', $isUpdate ? 'update' : 'write');

        $form = [
            'name'  => $validated['fullname'],
            'email' => $validated['email'],
        ];

        $pass = $validated['pass'] ?? null;
        if($pass)
            $form['password'] = bcrypt($pass);

        $selectedRoleName = null;
        if (! empty($validated['navigation_access'])) {
            try {
                $roleId = decrypt((string) $validated['navigation_access']);
                $selectedRole = Role::query()->where('id', $roleId)->where('guard_name', 'web')->first();
                $navigationAccess = NavigationAccess::query()->where('role_id', $roleId)->exists();
                if (! $selectedRole || ! $navigationAccess) {
                    return response()->json([
                        'code' => 0,
                        'msg' => 'Navigation access tidak valid.',
                        'data' => [],
                    ]);
                }

                $selectedRoleName = $selectedRole->name;
            } catch (\Throwable $th) {
                return response()->json([
                    'code' => 0,
                    'msg' => 'Navigation access tidak valid.',
                    'data' => [],
                ]);
            }
        }

        if($isUpdate)
        {
            $user = User::query()->where('id', decrypt($validated['id']))->first();
            if (! $user) {
                return response()->json([
                    'code' => 0,
                    'msg' => 'User tidak terdaftar.',
                    'data' => [],
                ]);
            }

            $user->update($form);
            $selectedRoleName ? $user->syncRoles([$selectedRoleName]) : $user->syncRoles([]);
        }else{
            $user = User::create([
                'name' => $validated['fullname'],
                'email' => $validated['email'],
                'email_verified_at' => now(),
                'password' => bcrypt($pass),
                'remember_token' => Str::random(10),
            ]);
            $selectedRoleName ? $user->syncRoles([$selectedRoleName]) : $user->syncRoles([]);
        }
        session()->flash('success', "Berhasil simpan User");
        return response()->json([
            'code'  => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }

    private function abortWhenUnauthorized(string $navigationKey, string $action = 'read'): void
    {
        if (! $this->_navigationService->CheckAccessNavigation(auth()->user(), $navigationKey, $action)) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }
    }
}
