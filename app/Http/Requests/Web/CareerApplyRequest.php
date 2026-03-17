<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Throwable;

class CareerApplyRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $careerId = null;

        if ($this->filled('formCareerId')) {
            try {
                $careerId = decrypt((string) $this->input('formCareerId'));
            } catch (Throwable) {
                $careerId = null;
            }
        }

        $this->merge([
            'decryptedCareerId' => $careerId,
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'formCareerId' => ['required', 'string'],
            'decryptedCareerId' => ['required', 'uuid', 'exists:career,id'],
            'formFirstName' => ['required', 'string', 'max:255'],
            'formLastName' => ['required', 'string', 'max:255'],
            'formEmail' => ['required', 'email', 'max:255'],
            'formPhone' => ['required', 'regex:/^\d{10,12}$/'],
            'formBod' => ['required', 'date'],
            'formLastEducation' => ['required', 'in:smk,diploma,s1,s2,s3'],
            'formMajor' => ['required', 'string', 'max:255'],
            'formIsExperience' => ['required', 'in:0,1'],
            'formCurrentSalary' => ['required', 'string', 'max:20'],
            'formExpectSalary' => ['required', 'string', 'max:20'],
            'formCV' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'totalRow' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'decryptedCareerId.required' => 'Lowongan tidak valid.',
            'decryptedCareerId.uuid' => 'Lowongan tidak valid.',
            'decryptedCareerId.exists' => 'Lowongan tidak valid.',
        ];
    }

    public function toPayload(): array
    {
        return [
            'careerid' => (string) $this->input('decryptedCareerId'),
            'firstname' => (string) $this->input('formFirstName'),
            'lastname' => (string) $this->input('formLastName'),
            'email' => (string) $this->input('formEmail'),
            'phone' => (string) $this->input('formPhone'),
            'bod' => (string) $this->input('formBod'),
            'lasteducation' => (string) $this->input('formLastEducation'),
            'major' => (string) $this->input('formMajor'),
            'isexperience' => $this->input('formIsExperience') === '1',
            'currentsalary' => (string) $this->input('formCurrentSalary'),
            'expectationsalary' => (string) $this->input('formExpectSalary'),
        ];
    }

    public function experienceRows(): array
    {
        $rows = [];
        $totalRow = (int) ($this->input('totalRow') ?? 0);

        for ($i = 1; $i <= $totalRow; $i++) {
            $rows[] = [
                'companyName' => $this->input("companyName{$i}"),
                'industri' => $this->input("industri{$i}"),
                'position' => $this->input("position{$i}"),
                'lengthOfWork' => $this->input("lengthOfWork{$i}"),
            ];
        }

        return $rows;
    }

    public function cvFile(): UploadedFile
    {
        /** @var UploadedFile $file */
        $file = $this->file('formCV');

        return $file;
    }
}
