<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Salon\Models\GiftCard;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\GiftCardResource;
use App\Http\Resources\Api\GiftCardTransactionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class GiftCardController extends Controller
{
    public function my(Request $request): AnonymousResourceCollection
    {
        return GiftCardResource::collection(
            GiftCard::query()
                ->where('owner_user_id', $request->user()->id)
                ->latest('id')
                ->get()
        );
    }

    public function show(Request $request, GiftCard $giftCard): GiftCardResource
    {
        if ((int) $giftCard->owner_user_id !== (int) $request->user()->id) {
            throw ValidationException::withMessages([
                'gift_card' => ['Gift card not found.'],
            ]);
        }

        return new GiftCardResource($giftCard->load('transactions'));
    }

    public function transactions(Request $request, GiftCard $giftCard): AnonymousResourceCollection
    {
        if ((int) $giftCard->owner_user_id !== (int) $request->user()->id) {
            throw ValidationException::withMessages([
                'gift_card' => ['Gift card not found.'],
            ]);
        }

        return GiftCardTransactionResource::collection(
            $giftCard->transactions()->latest('id')->get()
        );
    }
}
