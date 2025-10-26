<?php

namespace App\Http\Requests\v1\Balance;

use Illuminate\Foundation\Http\FormRequest;

class TransferBalanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from_user_id' => ['required', 'integer', 'exists:users,id'],
            'to_user_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'decimal:2', 'min:0'],
            'comment' => ['nullable', 'string'],
        ];
    }

    public function prepareForValidation(): void
    {
        $amount = $this->input('amount');

        if (is_numeric($amount)) {
            $this->merge([
                'amount' => number_format((float)$amount, 2, '.', '')
            ]);
        }
    }
}
