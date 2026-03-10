<?php

namespace App\Http\Requests\Admin;

class SaveHomeBannerMenuRequest extends AdminAjaxFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'mode' => ['required', 'in:beranda,about,product,resep,csr,news,career,contact'],
            'publish' => ['nullable', 'in:0,1'],
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }
}

