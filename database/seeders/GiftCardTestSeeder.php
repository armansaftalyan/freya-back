<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Application\Salon\Services\GiftCardService;
use App\Domain\Salon\Enums\GiftCardOrderStatus;
use App\Domain\Salon\Models\GiftCardOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class GiftCardTestSeeder extends Seeder
{
    public function run(): void
    {
        /** @var User|null $client */
        $client = User::query()->where('email', 'client@gmail.com')->first();

        /** @var GiftCardOrder $order */
        $order = GiftCardOrder::query()->updateOrCreate(
            ['provider_payment_id' => 'TEST-PAID-GIFT-ORDER-001'],
            [
                'buyer_user_id' => $client?->id,
                'recipient_name' => 'Test Client',
                'recipient_email' => 'gift-test@example.com',
                'recipient_phone' => '+37499111222',
                'amount' => 20000,
                'currency' => 'AMD',
                'payment_provider' => 'manual',
                'status' => GiftCardOrderStatus::Paid,
                'paid_at' => now(),
                'meta' => [
                    'seed' => 'GiftCardTestSeeder',
                    'notes' => 'Paid order for QR scan testing',
                ],
            ]
        );

        $giftCard = app(GiftCardService::class)->issueFromPaidOrder($order);

        // Keep a predictable token in seeded environments for QR testing.
        $seedToken = 'seed-gift-card-'.$order->id.'-test-token';
        $giftCard->forceFill([
            'qr_token' => $seedToken,
        ])->save();
    }
}
