<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function main(){

        return view("admin.main");
    }

    public function userList()
    {
        return view("admin.users", [
            'users' => User::all()
        ]);
    }

    public function getOne($id)
    {
        $id = decrypt($id);
        $user = User::where('id', $id)->first();
        return response()->json([
            'code' => $code = $user ? 200 : 0,
            'msg'   => !$code ? "User tidak terdaftar." : "",
            'data'  => [
                'name'  => $user->name,
                'email' => $user->email
            ]
        ]);
    }

    public function remove($id)
    {
        $id = decrypt($id);
        $user = User::where('id', $id)->first();

        if(!$user)
        {
            session()->flash("error", "User tidak terdaftar.");
        }else{
            if(strtolower($user->email) == "admin@admin.com")
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

    public function save()
    {
        $validate = Validator::make(request()->all(), [
            'fullname'  => "required",
            'email'     => "required|email"
        ]);

        if($validate->fails())
        {
            return response()->json([
                'code'  => 0,
                'msg'   => $validate->errors()->first(),
                'data'  => []
            ]);
        }

        if(!request()->input('id') && !request()->input('pass'))
            return response()->json([
                'code'  => 0,
                'msg'   => "Password tidak boleh kosong",
                'data'  => []
            ]);

        $form = [
            'name'  => request()->input('fullname'),
            'email' => request()->input('email')
        ];

        if($pass = request()->input("pass"))
            $form['password'] = bcrypt($pass);

        if(request()->input("id"))
        {
            User::where('id', decrypt(request()->input("id")))->update($form);
        }else{
            User::create([
                'name' => request()->input('fullname'),
                'email' => request()->input('email'),
                'email_verified_at' => now(),
                'password' => bcrypt($pass),
                'remember_token' => Str::random(10),
            ]);
        }
        session()->flash('success', "Berhasil simpan User");
        return response()->json([
            'code'  => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }
}
