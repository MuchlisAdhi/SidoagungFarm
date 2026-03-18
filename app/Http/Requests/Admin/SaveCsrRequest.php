<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SaveCsrRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'formTitle' => ['required', 'string', 'max:255'],
            'formPostedOn' => ['required', 'date'],
            'formThumbnail' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'formContent' => ['nullable', 'string'],
            'formPublish' => ['nullable', 'in:on,1,0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $keys = ['envKey', 'safetyKey', 'sosialKey'];
            $isEdit = false;
            foreach ($keys as $key) {
                if ($this->session()->has($key)) {
                    $isEdit = true;
                    break;
                }
            }

            if (! $isEdit && ! $this->hasFile('formThumbnail')) {
                $validator->errors()->add('formThumbnail', 'Thumbnail wajib diisi.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'formTitle.required' => 'Title wajib diisi.',
            'formTitle.max' => 'Title maksimal 255 karakter.',
            'formPostedOn.required' => 'Posting Date wajib diisi.',
            'formPostedOn.date' => 'Posting Date tidak valid.',
            'formThumbnail.file' => 'Thumbnail tidak valid.',
            'formThumbnail.image' => 'Thumbnail harus berupa gambar.',
            'formThumbnail.mimes' => 'Ekstensi thumbnail harus jpg, jpeg, atau png.',
            'formThumbnail.max' => 'Ukuran thumbnail maksimal 5 MB.',
            'formContent.string' => 'Content tidak valid.',
            'formPublish.in' => 'Nilai publish tidak valid.',
        ];
    }
}

