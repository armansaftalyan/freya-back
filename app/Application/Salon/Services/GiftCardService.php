<?php

declare(strict_types=1);

namespace App\Application\Salon\Services;

use App\Domain\Salon\Enums\GiftCardOrderStatus;
use App\Domain\Salon\Enums\GiftCardStatus;
use App\Domain\Salon\Enums\GiftCardTransactionType;
use App\Domain\Salon\Models\GiftCard;
use App\Domain\Salon\Models\GiftCardOrder;
use App\Domain\Salon\Models\GiftCardTransaction;
use App\Domain\Users\Models\User;
use App\Mail\GiftCardPreviewMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class GiftCardService
{
    public function issueFromPaidOrder(GiftCardOrder $order): GiftCard
    {
        if ($order->status !== GiftCardOrderStatus::Paid) {
            throw ValidationException::withMessages([
                'status' => ['Gift card order must be paid before issue.'],
            ]);
        }

        $existing = $order->giftCard;
        if ($existing !== null) {
            return $existing;
        }

        /** @var GiftCard $giftCard */
        $giftCard = GiftCard::query()->create([
            'code' => $this->generateCode(),
            'qr_token' => $this->generateQrToken(),
            'owner_user_id' => $order->buyer_user_id,
            'gift_card_order_id' => $order->id,
            'initial_amount' => $order->amount,
            'balance' => $order->amount,
            'currency' => $order->currency,
            'status' => GiftCardStatus::Active,
            'activated_at' => now(),
            'expires_at' => now()->addYear(),
            'meta' => [
                'recipient_name' => $order->recipient_name,
                'recipient_email' => $order->recipient_email,
                'recipient_phone' => $order->recipient_phone,
                'theme' => data_get($order->meta, 'theme', 'gold'),
                'locale' => data_get($order->meta, 'locale', app()->getLocale()),
            ],
        ]);

        GiftCardTransaction::query()->create([
            'gift_card_id' => $giftCard->id,
            'type' => GiftCardTransactionType::Issue,
            'amount' => $giftCard->initial_amount,
            'balance_after' => $giftCard->balance,
            'meta' => [
                'order_id' => $order->id,
                'payment_provider' => $order->payment_provider,
                'provider_payment_id' => $order->provider_payment_id,
            ],
        ]);

        $this->sendIssuedGiftCardEmail($giftCard, $order);

        return $giftCard;
    }

    public function findByQrToken(string $token): ?GiftCard
    {
        $normalizedToken = $this->normalizeQrToken($token);

        if ($normalizedToken === '') {
            return null;
        }

        return GiftCard::query()
            ->with(['owner', 'transactions'])
            ->where('qr_token', $normalizedToken)
            ->first();
    }

    public function redeem(
        GiftCard $giftCard,
        float $amount,
        ?User $performedBy = null,
        ?int $bookingOrderId = null,
        ?int $appointmentId = null,
        array $meta = [],
    ): GiftCardTransaction {
        $normalizedAmount = round(max(0, $amount), 2);
        if ($normalizedAmount <= 0) {
            throw ValidationException::withMessages([
                'amount' => ['Amount must be greater than zero.'],
            ]);
        }

        return DB::transaction(function () use ($giftCard, $normalizedAmount, $performedBy, $bookingOrderId, $appointmentId, $meta): GiftCardTransaction {
            /** @var GiftCard $locked */
            $locked = GiftCard::query()->whereKey($giftCard->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== GiftCardStatus::Active) {
                throw ValidationException::withMessages([
                    'gift_card' => ['Gift card is not active.'],
                ]);
            }

            if ($locked->expires_at !== null && $locked->expires_at->isPast()) {
                $locked->status = GiftCardStatus::Expired;
                $locked->save();

                throw ValidationException::withMessages([
                    'gift_card' => ['Gift card is expired.'],
                ]);
            }

            if ((float) $locked->balance < $normalizedAmount) {
                throw ValidationException::withMessages([
                    'amount' => ['Insufficient gift card balance.'],
                ]);
            }

            $newBalance = round((float) $locked->balance - $normalizedAmount, 2);
            $locked->balance = $newBalance;
            $locked->last_used_at = now();
            $locked->status = $newBalance <= 0 ? GiftCardStatus::Redeemed : GiftCardStatus::Active;
            $locked->save();

            /** @var GiftCardTransaction $transaction */
            $transaction = GiftCardTransaction::query()->create([
                'gift_card_id' => $locked->id,
                'type' => GiftCardTransactionType::Redeem,
                'amount' => $normalizedAmount,
                'balance_after' => $newBalance,
                'performed_by_user_id' => $performedBy?->id,
                'booking_order_id' => $bookingOrderId,
                'appointment_id' => $appointmentId,
                'meta' => $meta,
            ]);

            return $transaction;
        });
    }

    private function generateCode(): string
    {
        do {
            $code = 'FRYA-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4));
        } while (GiftCard::query()->where('code', $code)->exists());

        return $code;
    }

    private function generateQrToken(): string
    {
        do {
            $token = Str::random(64);
        } while (GiftCard::query()->where('qr_token', $token)->exists());

        return $token;
    }

    private function normalizeQrToken(string $value): string
    {
        $token = trim($value);
        if ($token === '') {
            return '';
        }

        $path = null;
        if (filter_var($token, FILTER_VALIDATE_URL)) {
            $path = parse_url($token, PHP_URL_PATH);
        } elseif (str_starts_with($token, '/')) {
            $path = $token;
        }

        if (is_string($path) && $path !== '') {
            $segments = array_values(array_filter(explode('/', trim($path, '/'))));
            if ($segments !== []) {
                $token = (string) end($segments);
            }
        }

        return urldecode(trim($token));
    }

    private function sendIssuedGiftCardEmail(GiftCard $giftCard, GiftCardOrder $order): void
    {
        $recipientEmail = trim((string) ($order->recipient_email ?? ''));
        if ($recipientEmail === '') {
            return;
        }

        try {
            Mail::to($recipientEmail)->send(new GiftCardPreviewMail(
                recipientName: trim((string) ($order->recipient_name ?? '')) ?: 'Customer',
                amount: (float) $giftCard->initial_amount,
                currency: (string) $giftCard->currency,
                code: (string) $giftCard->code,
                token: (string) $giftCard->qr_token,
                theme: (string) data_get($giftCard->meta, 'theme', 'gold'),
                mailLocale: (string) data_get($giftCard->meta, 'locale', app()->getLocale()),
            ));
        } catch (Throwable $exception) {
            Log::warning('Failed to send gift card email.', [
                'gift_card_id' => $giftCard->id,
                'gift_card_order_id' => $order->id,
                'recipient_email' => $recipientEmail,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
