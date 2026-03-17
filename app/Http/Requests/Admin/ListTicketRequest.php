<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ListTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'in:new,open,responded'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d'],
            'ticket_no' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function filters(): array
    {
        return [
            'status' => (string) ($this->input('status') ?? ''),
            'date_from' => (string) ($this->input('date_from') ?? ''),
            'date_to' => (string) ($this->input('date_to') ?? ''),
            'ticket_no' => trim((string) ($this->input('ticket_no') ?? '')),
        ];
    }
}
