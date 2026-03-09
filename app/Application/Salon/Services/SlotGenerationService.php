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
        $busyIntervals = $this->busyIntervalsForDay($master->id, $date);

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

                if (! $this->hasConflictInIntervals($busyIntervals, $start->getTimestamp(), $end->getTimestamp())) {
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

    /**
     * @return array<int, array{start:int,end:int}>
     */
    private function busyIntervalsForDay(int $masterId, Carbon $date): array
    {
        $dayStart = $date->copy()->startOfDay();
        $dayEnd = $date->copy()->endOfDay();

        return Appointment::query()
            ->where('master_id', $masterId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_at', '<', $dayEnd)
            ->where('end_at', '>', $dayStart)
            ->orderBy('start_at')
            ->get(['start_at', 'end_at'])
            ->map(fn (Appointment $appointment): array => [
                'start' => $appointment->start_at->getTimestamp(),
                'end' => $appointment->end_at->getTimestamp(),
            ])
            ->values()
            ->all();
    }

    /**
     * @param array<int, array{start:int,end:int}> $busyIntervals
     */
    private function hasConflictInIntervals(array $busyIntervals, int $startTs, int $endTs): bool
    {
        foreach ($busyIntervals as $interval) {
            if ($interval['start'] < $endTs && $interval['end'] > $startTs) {
                return true;
            }
        }

        return false;
    }
}
