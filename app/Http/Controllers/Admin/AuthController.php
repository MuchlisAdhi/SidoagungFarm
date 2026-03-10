<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if($request->isMethod("post"))
        {
            $cred = $request->validated();
            if(auth()->attempt($cred))
                return redirect()->route("admin.main");
            else
                session()->flash("error", "User's Not Found");
        }

        return view("admin.login");
    }

    public function logout()
    {
       session()->flush();
       auth()->logout();
       return redirect()->route("login");
    }
}
