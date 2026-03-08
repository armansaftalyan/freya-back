<?php

declare(strict_types=1);

namespace App\Application\Salon\Services;

use App\Domain\Salon\Models\Appointment;
use App\Domain\Salon\Models\Master;
use Carbon\Carbon;

class SlotGenerationService
{
    /**
     * @return array<int, array{start_at:string,end_at:string}>
     */
    public function generate(Master $master, int $durationMinutes, Carbon $date): array
    {
        $dayKey = strtolower($date->englishDayOfWeek);
        $masterRules = $master->schedule_rules[$dayKey] ?? [];

        if (empty($masterRules)) {
            $masterRules = [['start' => '10:00', 'end' => '19:00']];
        }

        $duration = max(1, $durationMinutes);
        $openTime = '10:00';
        $closeTime = '19:00';

        $slots = [];
        foreach ($masterRules as $rule) {
            $from = Carbon::parse($date->toDateString().' '.($rule['start'] ?? $openTime));
            $to = Carbon::parse($date->toDateString().' '.($rule['end'] ?? $closeTime));
            $openAt = Carbon::parse($date->toDateString().' '.$openTime);
            $closeAt = Carbon::parse($date->toDateString().' '.$closeTime);

            if ($from->lt($openAt)) {
                $from = $openAt->copy();
            }

            if ($to->gt($closeAt)) {
                $to = $closeAt->copy();
            }

            if ($to->lte($from)) {
                continue;
            }

            for ($cursor = $from->copy(); $cursor->copy()->addMinutes($duration)->lte($to); $cursor->addMinutes(30)) {
                $start = $cursor->copy();
                $end = $cursor->copy()->addMinutes($duration);

                if (! $this->hasConflict($master->id, $start, $end)) {
                    $slots[] = [
                        'start_at' => $start->toIso8601String(),
                        'end_at' => $end->toIso8601String(),
                    ];
                }
            }
        }

        return $slots;
    }

    public function hasConflict(int $masterId, Carbon $startAt, Carbon $endAt): bool
    {
        return Appointment::query()
            ->where('master_id', $masterId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($startAt, $endAt): void {
                $query->where('start_at', '<', $endAt)
                    ->where('end_at', '>', $startAt);
            })
            ->exists();
    }
}
