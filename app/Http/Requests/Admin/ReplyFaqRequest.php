<?php

namespace App\Http\Requests\Admin;

class ReplyFaqRequest extends AdminAjaxFormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'string'],
            'mode' => ['required', 'in:q1,q2'],
            'response' => ['required', 'string', 'max:5000'],
        ];
    }
}

