<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveCareerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'formPosition' => ['required', 'string', 'max:255'],
            'formLocation' => ['required', 'string', 'max:255'],
            'formDescription' => ['nullable', 'string'],
            'formQualification' => ['nullable', 'string'],
            'formPostedOn' => ['required', 'date'],
            'formClosingDate' => ['required', 'date', 'after_or_equal:formPostedOn'],
            'formPublish' => ['nullable', 'in:on,1,0'],
        ];
    }
}

