<?php

namespace App\Http\Requests\Admin;

class SaveHomeBannerRequest extends AdminAjaxFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'publish' => ['nullable', 'in:0,1'],
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }
}

