<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class SaveUserRequest extends AdminAjaxFormRequest
{
    public function rules(): array
    {
        $id = $this->input('id');
        $userId = null;

        if ($id) {
            try {
                $userId = decrypt((string) $id);
            } catch (\Throwable $th) {
                $userId = null;
            }
        }

        return [
            'id' => ['nullable', 'string'],
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'pass' => [
                'nullable',
                'string',
                'min:8',
                'max:255',
                'required_without:id',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).+$/',
            ],
            'navigation_access' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'pass.required_without' => 'Password tidak boleh kosong',
            'pass.min' => 'Password minimal 8 karakter.',
            'pass.regex' => 'Password wajib kombinasi huruf, angka, dan simbol.',
            'email.unique' => 'Email sudah digunakan user lain.',
        ];
    }
}

