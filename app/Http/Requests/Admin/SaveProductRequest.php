<?php

namespace App\Http\Requests\Admin;

class SaveProductRequest extends AdminAjaxFormRequest
{
    public function rules(): array
    {
        $id = $this->input('id');
        $imageRules = ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'];
        if (! $id) {
            array_unshift($imageRules, 'required');
        }

        return [
            'id' => ['nullable', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'publish' => ['nullable', 'in:0,1'],
            'description' => ['required', 'string'],
            'image' => $imageRules,
        ];
    }
}

