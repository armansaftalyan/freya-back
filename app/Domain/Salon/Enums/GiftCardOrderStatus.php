<?php

declare(strict_types=1);

namespace App\Domain\Salon\Enums;

enum GiftCardOrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
}
