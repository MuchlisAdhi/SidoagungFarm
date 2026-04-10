<?php

namespace App\Http\Requests\Web;

use App\Enums\QuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'formName' => ['required', 'string', 'max:255'],
            'formEmail' => ['required', 'email', 'max:255'],
            'formPhone' => ['required', 'string', 'max:30'],
            'formType' => ['required', Rule::in(array_values(array_unique([
                ...QuestionType::values(),
                'Kemitraan',
            ])))],
            'formDescription' => ['required', 'string', 'max:10000'],
        ];
    }

    public function toPayload(): array
    {
        return [
            'name' => (string) $this->input('formName'),
            'email' => (string) $this->input('formEmail'),
            'phone' => (string) $this->input('formPhone'),
            'qtype' => (string) ($this->input('formType') === 'Kemitraan'
                ? QuestionType::Kemitraan->value
                : $this->input('formType')),
            'description' => (string) $this->input('formDescription'),
        ];
    }
}
