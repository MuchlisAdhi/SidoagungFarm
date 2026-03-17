<?php

namespace App\Http\Requests\Web;

use Throwable;

class SubmitProductFaqRequest extends WebAjaxFormRequest
{
    protected function prepareForValidation(): void
    {
        $productId = null;

        if ($this->filled('productId')) {
            try {
                $productId = decrypt((string) $this->input('productId'));
            } catch (Throwable) {
                $productId = null;
            }
        }

        $this->merge([
            'decryptedProductId' => $productId,
        ]);
    }

    public function rules(): array
    {
        return [
            'productId' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'desc' => ['required', 'string', 'max:10000'],
            'decryptedProductId' => ['required', 'uuid', 'exists:product,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'decryptedProductId.required' => 'Produk tidak valid.',
            'decryptedProductId.uuid' => 'Produk tidak valid.',
            'decryptedProductId.exists' => 'Produk tidak valid.',
        ];
    }

    public function toPayload(): array
    {
        return [
            'productid' => (string) $this->input('decryptedProductId'),
            'name' => (string) $this->input('name'),
            'email' => (string) $this->input('email'),
            'phone' => (string) $this->input('phone'),
            'description' => (string) $this->input('desc'),
        ];
    }
}
