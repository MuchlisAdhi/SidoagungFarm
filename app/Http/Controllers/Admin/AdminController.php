<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveUserRequest;
use App\Models\User;
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

    public function save(SaveUserRequest $request)
    {
        $validated = $request->validated();

        $form = [
            'name'  => $validated['fullname'],
            'email' => $validated['email'],
        ];

        $pass = $validated['pass'] ?? null;
        if($pass)
            $form['password'] = bcrypt($pass);

        if(! empty($validated['id']))
        {
            User::where('id', decrypt($validated['id']))->update($form);
        }else{
            User::create([
                'name' => $validated['fullname'],
                'email' => $validated['email'],
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
