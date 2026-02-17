<?php

declare(strict_types=1);

namespace App\Application\Salon\Services;

use App\Domain\Salon\Models\Appointment;
use App\Domain\Salon\Models\Branch;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use Carbon\Carbon;

class SlotGenerationService
{
    /**
     * @return array<int, array{start_at:string,end_at:string}>
     */
    public function generate(Branch $branch, Master $master, Service $service, Carbon $date): array
    {
        $dayKey = strtolower($date->englishDayOfWeek);

        $branchRules = $branch->working_hours[$dayKey] ?? [];
        $masterRules = $master->schedule_rules[$dayKey] ?? $branchRules;

        if (empty($branchRules) || empty($masterRules)) {
            return [];
        }

        $duration = (int) $service->duration_minutes;

        $slots = [];
        foreach ($masterRules as $rule) {
            $from = Carbon::parse($date->toDateString().' '.$rule['start']);
            $to = Carbon::parse($date->toDateString().' '.$rule['end']);

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
