<?php

declare(strict_types=1);

namespace App\Admin\Filament\Widgets;

use App\Domain\Salon\Enums\GiftCardOrderStatus;
use App\Domain\Salon\Enums\GiftCardStatus;
use App\Domain\Salon\Enums\GiftCardTransactionType;
use App\Domain\Salon\Models\GiftCard;
use App\Domain\Salon\Models\GiftCardOrder;
use App\Domain\Salon\Models\GiftCardTransaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class GiftCardsPaymentsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $startWeek = Carbon::now()->startOfWeek();
        $endWeek = Carbon::now()->endOfWeek();

        $activeCards = GiftCard::query()
            ->where('status', GiftCardStatus::Active->value)
            ->count();

        $paidOrdersToday = GiftCardOrder::query()
            ->where('status', GiftCardOrderStatus::Paid->value)
            ->whereDate('paid_at', $today)
            ->count();

        $paidAmountWeek = (float) GiftCardOrder::query()
            ->where('status', GiftCardOrderStatus::Paid->value)
            ->whereBetween('paid_at', [$startWeek, $endWeek])
            ->sum('amount');

        $redeemedAmountWeek = (float) GiftCardTransaction::query()
            ->where('type', GiftCardTransactionType::Redeem->value)
            ->whereBetween('created_at', [$startWeek, $endWeek])
            ->sum('amount');

        return [
            Stat::make(__('messages.filament.widgets.gift_cards_active'), (string) $activeCards),
            Stat::make(__('messages.filament.widgets.gift_card_paid_orders_today'), (string) $paidOrdersToday),
            Stat::make(__('messages.filament.widgets.gift_card_paid_amount_week'), $this->formatAmd($paidAmountWeek)),
            Stat::make(__('messages.filament.widgets.gift_card_redeemed_amount_week'), $this->formatAmd($redeemedAmountWeek)),
        ];
    }

    private function formatAmd(float $amount): string
    {
        return number_format($amount, 0, '.', ' ').' AMD';
    }
}

