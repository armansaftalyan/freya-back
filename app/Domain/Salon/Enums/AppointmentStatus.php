<?php

declare(strict_types=1);

namespace App\Domain\Salon\Enums;

enum AppointmentStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Done = 'done';
    case NoShow = 'no_show';

    /** @return array<string, array<string>> */
    public static function transitions(): array
    {
        return [
            self::Pending->value => [self::Confirmed->value, self::Cancelled->value],
            self::Confirmed->value => [self::Done->value, self::Cancelled->value, self::NoShow->value],
            self::Cancelled->value => [],
            self::Done->value => [],
            self::NoShow->value => [],
        ];
    }

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::transitions()[$from] ?? [], true);
    }
}
