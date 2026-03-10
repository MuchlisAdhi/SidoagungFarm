<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveEmailConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'formId' => ['nullable', 'string'],
            'formName' => ['nullable', 'string', 'max:255'],
            'formHost' => ['required', 'string', 'max:255'],
            'formPort' => ['required', 'integer', 'min:1'],
            'formUsername' => ['required', 'string', 'max:255'],
            'formPassword' => ['nullable', 'string', 'max:255', 'required_without:formId'],
            'formEncryption' => ['nullable', 'string', 'max:50'],
            'formFromAddress' => ['required', 'email', 'max:255'],
            'formFromName' => ['required', 'string', 'max:255'],
            'formReport' => ['nullable', 'email', 'max:255'],
            'formIsActive' => ['nullable', 'in:on,1,0'],
        ];
    }

    public function messages(): array
    {
        return [
            'formPassword.required_without' => 'SMTP password wajib diisi untuk konfigurasi baru.',
        ];
    }
}
