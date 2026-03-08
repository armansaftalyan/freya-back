<?php

declare(strict_types=1);

namespace App\Application\Salon\Services;

use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class AutoAssignmentService
{
    public function __construct(private readonly SlotGenerationService $slotGenerationService)
    {
    }

    /**
     * @param Collection<int, array{service_id:int,master_id:int}> $items
     * @param Collection<int, Service> $services
     * @param Collection<int, Master> $masters
     * @return array<int, array{index:int,category_id:int,service_ids:array<int,int>,service_lines:array<int,array{duration_minutes:int,price:mixed,sort_order:int}>,candidates:array<int,array{duration:int,end_at_by_start:array<string,string>}>}>
     */
    public function buildGroups(Collection $items, Collection $services, Collection $masters, Carbon $date): array
    {
        $groupMap = [];

        foreach ($items as $idx => $item) {
            /** @var Service|null $service */
            $service = $services->get((int) $item['service_id']);
            if ($service === null) {
                throw ValidationException::withMessages([
                    "items.$idx.service_id" => [__('validation.exists', ['attribute' => "items.$idx.service_id"])],
                ]);
            }

            $categoryId = (int) $service->category_id;
            if (! isset($groupMap[$categoryId])) {
                $groupMap[$categoryId] = [
                    'index' => count($groupMap),
                    'category_id' => $categoryId,
                    'service_ids' => [],
                    'requested_master_ids' => [],
                ];
            }

            if (! in_array($service->id, $groupMap[$categoryId]['service_ids'], true)) {
                $groupMap[$categoryId]['service_ids'][] = (int) $service->id;
            }

            if ((int) ($item['master_id'] ?? 0) > 0) {
                $groupMap[$categoryId]['requested_master_ids'][] = (int) $item['master_id'];
            }
        }

        $groups = [];
        foreach ($groupMap as $group) {
            $requestedMasterIds = array_values(array_unique(array_filter(
                $group['requested_master_ids'],
                fn (int $id): bool => $id > 0
            )));

            if (count($requestedMasterIds) > 1) {
                throw ValidationException::withMessages([
                    'items' => [__('messages.appointment.service_not_available_for_master')],
                ]);
            }

            $fixedMasterId = $requestedMasterIds[0] ?? null;
            $serviceIds = $group['service_ids'];
            $candidateMap = [];

            /** @var Master $master */
            foreach ($masters as $master) {
                if ($fixedMasterId !== null && $master->id !== $fixedMasterId) {
                    continue;
                }

                $masterServices = $master->services->keyBy('id');
                $lineItems = [];
                $durationTotal = 0;
                $supportsAll = true;

                foreach ($serviceIds as $sortOrder => $serviceId) {
                    /** @var Service $service */
                    $service = $services->get($serviceId);
                    $masterService = $masterServices->get($serviceId);

                    if ($masterService === null) {
                        $supportsAll = false;
                        break;
                    }

                    $duration = max(1, (int) ($masterService->pivot?->duration_minutes ?? $service->duration_minutes));
                    $price = $masterService->pivot?->price ?? $service->price_from;
                    $durationTotal += $duration;
                    $lineItems[$serviceId] = [
                        'duration_minutes' => $duration,
                        'price' => $price,
                        'sort_order' => $sortOrder,
                    ];
                }

                if (! $supportsAll) {
                    continue;
                }

                $slots = $this->slotGenerationService->generate($master, $durationTotal, $date);
                $endAtByStart = [];
                foreach ($slots as $slot) {
                    $endAtByStart[(string) $slot['start_at']] = (string) $slot['end_at'];
                }

                $candidateMap[$master->id] = [
                    'duration' => $durationTotal,
                    'service_lines' => $lineItems,
                    'end_at_by_start' => $endAtByStart,
                ];
            }

            if ($candidateMap === []) {
                throw ValidationException::withMessages([
                    'items' => [__('messages.appointment.service_not_available_for_master')],
                ]);
            }

            $groups[] = [
                'index' => $group['index'],
                'category_id' => $group['category_id'],
                'service_ids' => $serviceIds,
                'candidates' => $candidateMap,
            ];
        }

        usort($groups, fn (array $a, array $b): int => $a['index'] <=> $b['index']);

        return $groups;
    }

    /**
     * @param array<int, array{index:int,category_id:int,service_ids:array<int,int>,candidates:array<int,array{duration:int,service_lines:array<int,array{duration_minutes:int,price:mixed,sort_order:int}>,end_at_by_start:array<string,string>}>}> $groups
     * @return array<int, array{start_at:string,end_at:string,assignments:array<int,array{category_id:int,selected_master_id:int,candidate_master_ids:array<int,int>,service_ids:array<int,int>,service_lines:array<int,array{duration_minutes:int,price:mixed,sort_order:int}>}>}>
     */
    public function findPlans(array $groups): array
    {
        if ($groups === []) {
            return [];
        }

        $startSets = [];
        foreach ($groups as $group) {
            $starts = [];
            foreach ($group['candidates'] as $candidate) {
                foreach ($candidate['end_at_by_start'] as $startAt => $_endAt) {
                    $starts[$startAt] = true;
                }
            }
            $startSets[] = array_keys($starts);
        }

        $commonStarts = $this->intersectStringSets($startSets);
        sort($commonStarts);

        $plans = [];
        foreach ($commonStarts as $startAt) {
            $plan = $this->buildPlanForStart($groups, $startAt);
            if ($plan !== null) {
                $plans[] = $plan;
            }
        }

        return $plans;
    }

    /**
     * @param array<int, array{index:int,category_id:int,service_ids:array<int,int>,candidates:array<int,array{duration:int,service_lines:array<int,array{duration_minutes:int,price:mixed,sort_order:int}>,end_at_by_start:array<string,string>}>}> $groups
     * @return array{start_at:string,end_at:string,assignments:array<int,array{category_id:int,selected_master_id:int,candidate_master_ids:array<int,int>,service_ids:array<int,int>,service_lines:array<int,array{duration_minutes:int,price:mixed,sort_order:int}>}>}|null
     */
    public function buildPlanForStart(array $groups, string $startAt): ?array
    {
        if ($groups === []) {
            return null;
        }

        $availableByGroup = [];
        foreach ($groups as $groupIdx => $group) {
            $availableByGroup[$groupIdx] = [];
            foreach ($group['candidates'] as $masterId => $candidate) {
                if (isset($candidate['end_at_by_start'][$startAt])) {
                    $availableByGroup[$groupIdx][$masterId] = [
                        'end_at' => $candidate['end_at_by_start'][$startAt],
                        'service_lines' => $candidate['service_lines'],
                    ];
                }
            }

            if ($availableByGroup[$groupIdx] === []) {
                return null;
            }
        }

        uasort($availableByGroup, fn (array $a, array $b): int => count($a) <=> count($b));
        $groupOrder = array_keys($availableByGroup);

        $usedMasters = [];
        $chosen = [];

        $assigned = $this->backtrackAssign($availableByGroup, $groupOrder, 0, $usedMasters, $chosen);
        if (! $assigned) {
            return null;
        }

        $maxEnd = null;
        $assignments = [];
        foreach ($chosen as $groupIdx => $masterId) {
            $group = $groups[$groupIdx];
            $candidate = $availableByGroup[$groupIdx][$masterId];
            $endAt = Carbon::parse((string) $candidate['end_at']);
            $maxEnd = $maxEnd === null || $endAt->gt($maxEnd) ? $endAt : $maxEnd;

            $candidateMasterIds = array_map('intval', array_keys($availableByGroup[$groupIdx]));
            sort($candidateMasterIds);

            $assignments[] = [
                'category_id' => $group['category_id'],
                'selected_master_id' => (int) $masterId,
                'candidate_master_ids' => $candidateMasterIds,
                'service_ids' => $group['service_ids'],
                'service_lines' => $candidate['service_lines'],
            ];
        }

        usort($assignments, fn (array $a, array $b): int => $a['category_id'] <=> $b['category_id']);

        return [
            'start_at' => $startAt,
            'end_at' => $maxEnd?->toIso8601String() ?? $startAt,
            'assignments' => $assignments,
        ];
    }

    /**
     * @param array<int, array<string, array{end_at:string,service_lines:array<int,array{duration_minutes:int,price:mixed,sort_order:int}>}>> $availableByGroup
     * @param array<int, int|string> $groupOrder
     * @param array<int, bool> $usedMasters
     * @param array<int, int> $chosen
     */
    private function backtrackAssign(array $availableByGroup, array $groupOrder, int $cursor, array &$usedMasters, array &$chosen): bool
    {
        if ($cursor >= count($groupOrder)) {
            return true;
        }

        $groupIdx = (int) $groupOrder[$cursor];
        $masters = array_keys($availableByGroup[$groupIdx]);
        sort($masters);

        foreach ($masters as $masterId) {
            $masterId = (int) $masterId;
            if (($usedMasters[$masterId] ?? false) === true) {
                continue;
            }

            $usedMasters[$masterId] = true;
            $chosen[$groupIdx] = $masterId;

            if ($this->backtrackAssign($availableByGroup, $groupOrder, $cursor + 1, $usedMasters, $chosen)) {
                return true;
            }

            unset($usedMasters[$masterId], $chosen[$groupIdx]);
        }

        return false;
    }

    /**
     * @param array<int, array<int, string>> $sets
     * @return array<int, string>
     */
    private function intersectStringSets(array $sets): array
    {
        if ($sets === []) {
            return [];
        }

        $acc = array_fill_keys($sets[0], true);
        foreach (array_slice($sets, 1) as $set) {
            $next = array_fill_keys($set, true);
            $acc = array_intersect_key($acc, $next);
            if ($acc === []) {
                return [];
            }
        }

        return array_keys($acc);
    }
}
