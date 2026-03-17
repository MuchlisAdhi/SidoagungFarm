<?php

namespace App\Http\Requests\Admin;

class FaqGetRequest extends AdminAjaxFormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'string'],
            'mode' => ['required', 'in:q1,q2'],
        ];
    }
}
