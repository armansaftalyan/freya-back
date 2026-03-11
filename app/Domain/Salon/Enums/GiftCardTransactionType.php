<?php

declare(strict_types=1);

namespace App\Domain\Salon\Enums;

enum GiftCardTransactionType: string
{
    case Issue = 'issue';
    case Redeem = 'redeem';
    case Refund = 'refund';
    case Adjust = 'adjust';
    case Expire = 'expire';
}
