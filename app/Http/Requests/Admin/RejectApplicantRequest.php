<?php

namespace App\Http\Requests\Admin;

class RejectApplicantRequest extends AdminAjaxFormRequest
{
    public function rules(): array
    {
        return [
            'id' => ['required', 'string'],
            'reason' => ['required', 'string', 'max:2000'],
        ];
    }
}

