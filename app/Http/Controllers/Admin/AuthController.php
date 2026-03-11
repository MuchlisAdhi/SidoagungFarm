<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Services\Contracts\INavigationService;

class AuthController extends Controller
{
    private INavigationService $_navigationService;

    public function __construct(INavigationService $navigationService)
    {
        $this->_navigationService = $navigationService;
    }

    public function login(LoginRequest $request)
    {
        if($request->isMethod("post"))
        {
            $cred = $request->validated();
            if(auth()->attempt($cred)) {
                $user = auth()->user();

                if ($this->_navigationService->CheckAccessNavigation($user, 'home', 'read')) {
                    return redirect()->route("admin.main");
                }

                $redirectUrl = $this->resolveFirstAccessibleUrl(
                    $this->_navigationService->GetAccessNavigation($user)
                );

                return $redirectUrl
                    ? redirect()->to($redirectUrl)
                    : redirect()->route("admin.unauthorized");
            } else {
                session()->flash("error", "User's Not Found");
            }
        }

        return view("admin.login");
    }

    public function logout()
    {
       session()->flush();
       auth()->logout();
       return redirect()->route("login");
    }

    public function unauthorized()
    {
        return response()->view('errors.403', [
            'message' => 'Anda tidak memiliki akses ke halaman yang diminta.',
        ], 403);
    }

    private function resolveFirstAccessibleUrl(array $menus): ?string
    {
        foreach ($menus as $menu) {
            $childs = $menu['childs'] ?? [];
            if (count($childs)) {
                foreach ($childs as $child) {
                    $url = (string) ($child['url'] ?? '');
                    if ($url !== '') {
                        return $url;
                    }
                }
            }

            $url = (string) ($menu['url'] ?? '');
            if ($url !== '') {
                return $url;
            }
        }

        return null;
    }
}
