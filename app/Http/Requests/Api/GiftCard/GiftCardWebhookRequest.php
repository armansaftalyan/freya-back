<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\GiftCard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GiftCardWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        $expectedToken = (string) config('services.gift_cards.webhook_token', '');
        if ($expectedToken === '') {
            return false;
        }

        $receivedToken = (string) $this->header('X-Gift-Card-Webhook-Token', '');
        return hash_equals($expectedToken, $receivedToken);
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:gift_card_orders,id'],
            'status' => ['required', Rule::in(['paid', 'failed', 'refunded'])],
            'provider_payment_id' => ['nullable', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
