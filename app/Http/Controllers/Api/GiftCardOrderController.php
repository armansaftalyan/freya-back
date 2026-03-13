<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Salon\Services\GiftCardService;
use App\Domain\Salon\Enums\GiftCardOrderStatus;
use App\Domain\Salon\Models\GiftCardOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GiftCard\GiftCardWebhookRequest;
use App\Http\Requests\Api\GiftCard\StoreGiftCardOrderRequest;
use App\Http\Resources\Api\GiftCardOrderResource;
use Illuminate\Http\JsonResponse;

class GiftCardOrderController extends Controller
{
    public function __construct(private readonly GiftCardService $giftCardService)
    {
    }

    public function store(StoreGiftCardOrderRequest $request): JsonResponse
    {
        $requestedAmount = (float) $request->input('amount');
        $paymentProvider = $request->string('payment_provider')->toString() ?: 'manual';
        $testForcedAmount = (float) config('services.idram.test_force_amount', 0);
        $idramPayableAmount = $paymentProvider === 'idram' && $testForcedAmount > 0 ? $testForcedAmount : $requestedAmount;

        $meta = array_merge(
            (array) $request->input('meta', []),
            [
                'requested_amount' => $requestedAmount,
                'sender_name' => $request->string('sender_name')->toString() ?: null,
                'sender_email' => $request->string('sender_email')->toString() ?: null,
                'message' => $request->string('message')->toString() ?: null,
            ],
        );

        /** @var GiftCardOrder $order */
        $order = GiftCardOrder::query()->create([
            'buyer_user_id' => $request->user()?->id,
            'recipient_name' => $request->string('recipient_name')->toString() ?: null,
            'recipient_email' => $request->string('recipient_email')->toString() ?: null,
            'recipient_phone' => $request->string('recipient_phone')->toString() ?: null,
            'amount' => $requestedAmount,
            'currency' => strtoupper($request->string('currency')->toString() ?: 'AMD'),
            'payment_provider' => $paymentProvider,
            'status' => GiftCardOrderStatus::Pending,
            'meta' => $meta,
        ]);

        $paymentStatus = 'pending';
        $paymentMessage = 'Order created. Complete payment with your provider and send webhook to activate gift card.';
        $paymentPayload = null;

        if ($order->payment_provider === 'idram') {
            $idramAccount = (string) config('services.idram.rec_account', '');
            if ($idramAccount === '') {
                $paymentStatus = 'failed';
                $paymentMessage = 'Idram is not configured on the server.';
            } else {
                $paymentStatus = 'redirect';
                $paymentMessage = 'Redirect customer to Idram and wait for RESULT callback.';
                $paymentPayload = [
                    'action' => (string) config('services.idram.action_url', 'https://banking.idram.am/Payment/GetPayment'),
                    'method' => 'POST',
                    'fields' => [
                        'EDP_LANGUAGE' => 'AM',
                        'EDP_REC_ACCOUNT' => $idramAccount,
                        'EDP_DESCRIPTION' => sprintf('Freya gift card order #%d', $order->id),
                        'EDP_AMOUNT' => number_format($idramPayableAmount, 2, '.', ''),
                        'EDP_BILL_NO' => (string) $order->id,
                        'EDP_EMAIL' => $order->recipient_email,
                    ],
                ];

                $order->meta = array_merge(
                    (array) ($order->meta ?? []),
                    [
                        'idram' => [
                            'expected_amount' => number_format($idramPayableAmount, 2, '.', ''),
                            'forced_test_amount' => $paymentProvider === 'idram' && $testForcedAmount > 0,
                        ],
                    ],
                );
                $order->save();
            }
        }

        if ((bool) config('services.gift_cards.auto_mark_paid', false)) {
            $order->status = GiftCardOrderStatus::Paid;
            $order->paid_at = now();
            $order->save();

            $giftCard = $this->giftCardService->issueFromPaidOrder($order);
            $order->setRelation('giftCard', $giftCard);

            $paymentStatus = 'paid';
            $paymentMessage = 'Payment auto-confirmed and gift card issued.';
        }

        return response()->json([
            'data' => new GiftCardOrderResource($order->loadMissing('giftCard')),
            'payment' => [
                'status' => $paymentStatus,
                'message' => $paymentMessage,
                'payload' => $paymentPayload,
            ],
        ], 201);
    }

    public function webhook(GiftCardWebhookRequest $request): JsonResponse
    {
        /** @var GiftCardOrder $order */
        $order = GiftCardOrder::query()->findOrFail((int) $request->input('order_id'));
        $status = (string) $request->input('status');

        $order->status = GiftCardOrderStatus::from($status);
        $order->provider_payment_id = (string) ($request->input('provider_payment_id') ?? $order->provider_payment_id);
        $order->meta = array_merge((array) ($order->meta ?? []), (array) $request->input('meta', []));
        $order->paid_at = $status === GiftCardOrderStatus::Paid->value ? now() : null;
        $order->save();

        if ($order->status === GiftCardOrderStatus::Paid) {
            $giftCard = $this->giftCardService->issueFromPaidOrder($order);
            $order->setRelation('giftCard', $giftCard);
        }

        return response()->json([
            'data' => new GiftCardOrderResource($order->load('giftCard')),
        ]);
    }
}
