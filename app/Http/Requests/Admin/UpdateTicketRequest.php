<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'formStatus' => ['required', 'in:new,open,responded'],
            'formPriority' => ['required', 'in:low,normal,high'],
            'formChannel' => ['required', 'string', 'max:50'],
            'formResponse' => ['nullable', 'string'],
        ];
    }
}

