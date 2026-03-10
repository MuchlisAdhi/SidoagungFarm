<?php

namespace App\Http\Requests\Admin;

class SaveTestimoniRequest extends AdminAjaxFormRequest
{
    public function rules(): array
    {
        return [
            'formName' => ['required', 'string', 'max:255'],
            'formTitle' => ['required', 'string', 'max:255'],
            'formTestimoni' => ['required', 'string'],
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }
}

