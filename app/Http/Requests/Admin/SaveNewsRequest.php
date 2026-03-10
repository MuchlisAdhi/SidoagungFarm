<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SaveNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'formTitle' => ['required', 'string', 'max:255'],
            'formAuthor' => ['required', 'string', 'max:255'],
            'formPostedOn' => ['required', 'date'],
            'formThumbnail' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'formContent' => ['nullable', 'string'],
            'formPublish' => ['nullable', 'in:on,1,0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->session()->has('newsKey') && ! $this->hasFile('formThumbnail')) {
                $validator->errors()->add('formThumbnail', 'Thumbnail wajib diisi.');
            }
        });
    }
}

