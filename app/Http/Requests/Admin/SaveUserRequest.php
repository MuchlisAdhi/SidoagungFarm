<?php

namespace App\Http\Requests\Admin;

class SaveUserRequest extends AdminAjaxFormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['nullable', 'string'],
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'pass' => ['nullable', 'string', 'max:255', 'required_without:id'],
            'navigation_access' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'pass.required_without' => 'Password tidak boleh kosong',
        ];
    }
}

