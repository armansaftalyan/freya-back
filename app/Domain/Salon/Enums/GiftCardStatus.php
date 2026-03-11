<?php

declare(strict_types=1);

namespace App\Domain\Salon\Enums;

enum GiftCardStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Redeemed = 'redeemed';
    case Expired = 'expired';
    case Blocked = 'blocked';
    case Cancelled = 'cancelled';
}
