<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Application\Salon\Services\GiftCardService;
use App\Domain\Salon\Models\GiftCard;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GiftCard\RedeemGiftCardRequest;
use App\Http\Resources\Api\GiftCardResource;
use App\Http\Resources\Api\GiftCardTransactionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GiftCardAdminController extends Controller
{
    public function __construct(private readonly GiftCardService $giftCardService)
    {
    }

    public function scan(Request $request, string $token): JsonResponse
    {
        $this->assertCanManageGiftCards($request);

        $giftCard = $this->giftCardService->findByQrToken($token);
        if ($giftCard === null) {
            throw ValidationException::withMessages([
                'token' => ['Gift card not found.'],
            ]);
        }

        return response()->json([
            'data' => new GiftCardResource($giftCard),
        ]);
    }

    public function redeem(RedeemGiftCardRequest $request, GiftCard $giftCard): JsonResponse
    {
        $this->assertCanManageGiftCards($request);

        $transaction = $this->giftCardService->redeem(
            giftCard: $giftCard,
            amount: (float) $request->input('amount'),
            performedBy: $request->user(),
            bookingOrderId: $request->integer('booking_order_id') ?: null,
            appointmentId: $request->integer('appointment_id') ?: null,
            meta: (array) $request->input('meta', []),
        );

        return response()->json([
            'data' => new GiftCardTransactionResource($transaction),
            'gift_card' => new GiftCardResource($giftCard->fresh()),
        ]);
    }

    private function assertCanManageGiftCards(Request $request): void
    {
        $user = $request->user();

        if ($user === null || ! $user->hasAnyRole(['admin', 'manager'])) {
            abort(403);
        }
    }
}
