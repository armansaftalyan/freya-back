<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\GiftCard;

use Illuminate\Foundation\Http\FormRequest;

class RedeemGiftCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'booking_order_id' => ['nullable', 'integer', 'exists:booking_orders,id'],
            'appointment_id' => ['nullable', 'integer', 'exists:appointments,id'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
