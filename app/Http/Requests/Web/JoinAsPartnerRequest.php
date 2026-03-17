<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class JoinAsPartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'formFirstName' => ['required', 'string', 'max:255'],
            'formLastName' => ['required', 'string', 'max:255'],
            'formBod' => ['required', 'date'],
            'formPhone' => ['required', 'string', 'max:30'],
            'formEmail' => ['required', 'email', 'max:255'],
            'formCategory' => ['required', 'in:Kemitraan'],
            'formCompanyName' => ['required', 'string', 'max:255'],
            'formCompanyLocation' => ['required', 'string', 'max:255'],
            'formCompanyDescription' => ['required', 'string', 'max:10000'],
        ];
    }

    public function toPayload(): array
    {
        return [
            'firstname' => (string) $this->input('formFirstName'),
            'lastname' => (string) $this->input('formLastName'),
            'bod' => (string) $this->input('formBod'),
            'phone' => (string) $this->input('formPhone'),
            'email' => (string) $this->input('formEmail'),
            'category' => (string) $this->input('formCategory'),
            'companyname' => (string) $this->input('formCompanyName'),
            'companylocation' => (string) $this->input('formCompanyLocation'),
            'companydescription' => (string) $this->input('formCompanyDescription'),
        ];
    }
}
