<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        if(request()->isMethod("post"))
        {
            $validator = Validator::make(request()->all(), [
                "email" => "required",
                "password"  => "required"
            ]);

            if($validator->fails())
            {
                session()->flash("error", $validator->errors()->first());
                return redirect()->route("login");
            }

            $cred = request()->only('email', 'password');
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
