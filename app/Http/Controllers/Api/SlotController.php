<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Salon\Services\AutoAssignmentService;
use App\Application\Salon\Services\SlotGenerationService;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Slot\ComboSlotsRequest;
use App\Http\Requests\Api\Slot\SlotsQueryRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SlotController extends Controller
{
    public function __construct(
        private readonly SlotGenerationService $slotGenerationService,
        private readonly AutoAssignmentService $autoAssignmentService,
    ) {}

    public function index(SlotsQueryRequest $request): JsonResponse
    {
        $master = Master::query()->with('services')->findOrFail($request->integer('master_id'));
        $date = Carbon::createFromFormat('Y-m-d', (string) $request->string('date'));
        $serviceIds = collect((array) $request->input('service_ids'))
            ->map(fn ($value) => (int) $value)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        $services = Service::query()->whereIn('id', $serviceIds)->get()->keyBy('id');
        if ($services->count() !== $serviceIds->count()) {
            throw ValidationException::withMessages([
                'service_ids' => [__('validation.exists', ['attribute' => 'service_ids'])],
            ]);
        }

        $masterServices = $master->services->keyBy('id');
        foreach ($serviceIds as $serviceId) {
            if (! $masterServices->has($serviceId)) {
                throw ValidationException::withMessages([
                    'service_ids' => [__('messages.appointment.service_not_available_for_master')],
                ]);
            }
        }

        $duration = 0;
        foreach ($serviceIds as $serviceId) {
            $service = $services->get($serviceId);
            $masterService = $masterServices->get($serviceId);
            $duration += (int) ($masterService?->pivot?->duration_minutes ?? $service?->duration_minutes ?? 0);
        }

        $slots = $this->slotGenerationService->generate($master, max(1, $duration), $date);

        return response()->json(['data' => $slots]);
    }

    public function combo(ComboSlotsRequest $request): JsonResponse
    {
        $items = collect((array) $request->input('items'))
            ->map(fn (array $item): array => [
                'service_id' => (int) ($item['service_id'] ?? 0),
                'master_id' => (int) ($item['master_id'] ?? 0),
            ])
            ->filter(fn (array $item): bool => $item['service_id'] > 0)
            ->values();

        if ($items->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $date = Carbon::createFromFormat('Y-m-d', (string) $request->string('date'));
        $serviceIds = $items->pluck('service_id')->unique()->values();
        $services = Service::query()->whereIn('id', $serviceIds)->get()->keyBy('id');

        if ($services->count() !== $serviceIds->count()) {
            throw ValidationException::withMessages([
                'items' => [__('validation.exists', ['attribute' => 'items'])],
            ]);
        }

        $masterIds = $items
            ->pluck('master_id')
            ->map(fn (int $id): int => max(0, $id))
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        $mastersQuery = Master::query()->with('services')->where('is_active', true);
        if ($masterIds->isNotEmpty()) {
            $mastersQuery->where(function ($query) use ($masterIds, $serviceIds): void {
                $query->whereIn('id', $masterIds)
                    ->orWhereHas('services', fn ($serviceQuery) => $serviceQuery->whereIn('services.id', $serviceIds));
            });
        } else {
            $mastersQuery->whereHas('services', fn ($serviceQuery) => $serviceQuery->whereIn('services.id', $serviceIds));
        }

        $masters = $mastersQuery->get()->keyBy('id');

        if ($masters->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $groups = $this->autoAssignmentService->buildGroups($items, $services, $masters, $date);
        $plans = $this->autoAssignmentService->findPlans($groups);
        $result = array_map(
            fn (array $plan): array => [
                'start_at' => $plan['start_at'],
                'end_at' => $plan['end_at'],
            ],
            $plans
        );

        return response()->json(['data' => $result]);
    }
}
