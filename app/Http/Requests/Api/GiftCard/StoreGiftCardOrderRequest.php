<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\GiftCard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGiftCardOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:10000', 'max:200000'],
            'currency' => ['nullable', 'string', 'max:8'],
            'payment_provider' => ['nullable', 'string', 'max:64', Rule::in(['manual', 'idram'])],
            'theme' => ['nullable', 'string', 'max:32', Rule::in(['gold', 'black', 'rose'])],
            'locale' => ['nullable', 'string', 'max:8', Rule::in(['ru', 'en', 'hy'])],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'recipient_email' => ['nullable', 'email', 'max:255', 'required_without:recipient_phone'],
            'recipient_phone' => ['nullable', 'string', 'max:32', 'required_without:recipient_email'],
            'sender_name' => ['nullable', 'string', 'max:255'],
            'sender_email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:1500'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
