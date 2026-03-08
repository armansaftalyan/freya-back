<?php

declare(strict_types=1);

namespace App\Application\Salon\Services;

use App\Domain\Salon\Enums\AppointmentStatus;
use App\Domain\Salon\Models\Appointment;
use App\Domain\Salon\Models\BookingOrder;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use App\Domain\Users\Models\User;
use App\Infrastructure\Notifications\AppointmentNotifier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function __construct(
        private readonly SlotGenerationService $slotGenerationService,
        private readonly AutoAssignmentService $autoAssignmentService,
        private readonly AppointmentNotifier $appointmentNotifier,
    ) {
    }

    /** @param array{master_id:int,service_id?:int,service_ids?:array<int,int>,start_at:string,comment?:string,source?:string,guest_name?:string,guest_phone?:string,booking_order_id?:int|null} $payload */
    public function create(User $client, array $payload): Appointment
    {
        $serviceIds = collect($payload['service_ids'] ?? [])
            ->map(fn ($value) => (int) $value)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        if ($serviceIds->isEmpty() && isset($payload['service_id'])) {
            $serviceIds = collect([(int) $payload['service_id']]);
        }

        if ($serviceIds->isEmpty()) {
            throw ValidationException::withMessages([
                'service_ids' => [__('validation.required', ['attribute' => 'service_ids'])],
            ]);
        }

        /** @var Master $master */
        $master = Master::query()->with('services')->findOrFail($payload['master_id']);
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

        $startAt = Carbon::parse($payload['start_at']);
        $durationTotal = 0;
        $lineItems = [];
        foreach ($serviceIds as $index => $serviceId) {
            /** @var Service $service */
            $service = $services->get($serviceId);
            $masterService = $masterServices->get($serviceId);
            $duration = (int) ($masterService?->pivot?->duration_minutes ?? $service->duration_minutes);
            $price = $masterService?->pivot?->price ?? $service->price_from;

            $durationTotal += $duration;
            $lineItems[$serviceId] = [
                'duration_minutes' => $duration,
                'price' => $price,
                'sort_order' => $index,
            ];
        }

        $endAt = $startAt->copy()->addMinutes($durationTotal);

        if ($this->slotGenerationService->hasConflict($master->id, $startAt, $endAt)) {
            throw ValidationException::withMessages([
                'start_at' => [__('messages.appointment.slot_occupied')],
            ]);
        }

        /** @var Appointment $appointment */
        $appointment = Appointment::query()->create([
            'booking_order_id' => isset($payload['booking_order_id']) ? (int) $payload['booking_order_id'] : null,
            'client_id' => $client->id,
            'master_id' => $master->id,
            'service_id' => (int) $serviceIds->first(),
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => AppointmentStatus::Pending,
            'comment' => $payload['comment'] ?? null,
            'source' => $payload['source'] ?? 'site',
        ]);

        $appointment->services()->sync($lineItems);

        $this->appointmentNotifier->notifyCreated($appointment);

        return $appointment;
    }

    /** @param array{items:array<int,array{service_id:int,master_id?:int|null}>,start_at:string,comment?:string,source?:string,booking_order_id?:int|null} $payload
     *  @return array<int, Appointment>
     */
    public function createMany(User $client, array $payload): array
    {
        $items = collect((array) ($payload['items'] ?? []))
            ->map(fn (array $item): array => [
                'service_id' => (int) ($item['service_id'] ?? 0),
                'master_id' => (int) ($item['master_id'] ?? 0),
            ])
            ->filter(fn (array $item): bool => $item['service_id'] > 0)
            ->values();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => [__('validation.required', ['attribute' => 'items'])],
            ]);
        }

        $services = Service::query()
            ->whereIn('id', $items->pluck('service_id')->unique()->values())
            ->get()
            ->keyBy('id');
        if ($services->count() !== $items->pluck('service_id')->unique()->count()) {
            throw ValidationException::withMessages([
                'items' => [__('validation.exists', ['attribute' => 'items'])],
            ]);
        }

        $startAt = Carbon::parse($payload['start_at']);
        $date = Carbon::createFromFormat('Y-m-d', $startAt->toDateString());
        $explicitMasterIds = $items
            ->pluck('master_id')
            ->map(fn (int $id): int => max(0, $id))
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        $mastersQuery = Master::query()->with('services')->where('is_active', true);
        if ($explicitMasterIds->isNotEmpty()) {
            $mastersQuery->where(function ($query) use ($explicitMasterIds, $services): void {
                $query->whereIn('id', $explicitMasterIds)
                    ->orWhereHas('services', fn ($serviceQuery) => $serviceQuery->whereIn('services.id', $services->keys()));
            });
        } else {
            $mastersQuery->whereHas('services', fn ($serviceQuery) => $serviceQuery->whereIn('services.id', $services->keys()));
        }

        $masters = $mastersQuery->get()->keyBy('id');
        $groups = $this->autoAssignmentService->buildGroups($items, $services, $masters, $date);
        $plan = $this->autoAssignmentService->buildPlanForStart($groups, $startAt->toIso8601String());
        if ($plan === null) {
            throw ValidationException::withMessages([
                'start_at' => [__('messages.appointment.slot_not_available')],
            ]);
        }

        $appointments = [];

        DB::transaction(function () use (&$appointments, $client, $payload, $masters, $services, $plan, $startAt): void {
            foreach ($plan['assignments'] as $assignment) {
                $candidateMasterIds = (array) ($assignment['candidate_master_ids'] ?? []);
                $selectedMasterId = (int) ($assignment['selected_master_id'] ?? 0);
                $isAmbiguous = count($candidateMasterIds) > 1;
                $masterId = $isAmbiguous ? null : $selectedMasterId;

                /** @var Master|null $master */
                $master = $masterId !== null ? $masters->get($masterId) : null;
                if ($masterId !== null && $master === null) {
                    throw ValidationException::withMessages([
                        'items' => [__('validation.exists', ['attribute' => 'items'])],
                    ]);
                }

                $lineItems = (array) ($assignment['service_lines'] ?? []);
                $serviceIds = array_keys($lineItems);
                if ($isAmbiguous) {
                    $serviceIds = (array) ($assignment['service_ids'] ?? []);
                    $lineItems = [];
                    foreach ($serviceIds as $sortOrder => $serviceId) {
                        /** @var Service|null $service */
                        $service = $services->get((int) $serviceId);
                        if ($service === null) {
                            throw ValidationException::withMessages([
                                'items' => [__('validation.exists', ['attribute' => 'items'])],
                            ]);
                        }

                        $lineItems[(int) $serviceId] = [
                            'duration_minutes' => max(1, (int) $service->duration_minutes),
                            'price' => $service->price_from,
                            'sort_order' => $sortOrder,
                        ];
                    }
                }

                $durationTotal = array_reduce(
                    $lineItems,
                    fn (int $carry, array $line): int => $carry + max(1, (int) ($line['duration_minutes'] ?? 0)),
                    0
                );
                $endAt = $startAt->copy()->addMinutes(max(1, $durationTotal));

                if ($master !== null && $this->slotGenerationService->hasConflict($master->id, $startAt, $endAt)) {
                    throw ValidationException::withMessages([
                        'start_at' => [__('messages.appointment.slot_occupied')],
                    ]);
                }

                /** @var Appointment $appointment */
                $appointment = Appointment::query()->create([
                    'booking_order_id' => isset($payload['booking_order_id']) ? (int) $payload['booking_order_id'] : null,
                    'client_id' => $client->id,
                    'master_id' => $master?->id,
                    'service_id' => (int) ($serviceIds[0] ?? 0),
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'status' => AppointmentStatus::Pending,
                    'comment' => $payload['comment'] ?? null,
                    'source' => $payload['source'] ?? 'site',
                ]);

                $appointment->services()->sync($lineItems);
                $this->appointmentNotifier->notifyCreated($appointment);
                $appointments[] = $appointment;
            }
        });

        return $appointments;
    }

    /** @param array{
     *   lines:array<int,array{
     *      start_at:string,
     *      items:array<int,array{service_id:int,master_id?:int|null}>
     *   }>,
     *   comment?:string,
     *   source?:string
     * } $payload
     */
    public function createOrder(User $client, array $payload): BookingOrder
    {
        $lines = collect((array) ($payload['lines'] ?? []))
            ->filter(fn (mixed $line): bool => is_array($line))
            ->map(fn (array $line): array => [
                'start_at' => (string) ($line['start_at'] ?? ''),
                'items' => collect((array) ($line['items'] ?? []))
                    ->filter(fn (mixed $item): bool => is_array($item))
                    ->map(fn (array $item): array => [
                        'service_id' => (int) ($item['service_id'] ?? 0),
                        'master_id' => isset($item['master_id']) ? (int) $item['master_id'] : null,
                    ])
                    ->filter(fn (array $item): bool => $item['service_id'] > 0)
                    ->values()
                    ->all(),
            ])
            ->filter(fn (array $line): bool => $line['start_at'] !== '' && ! empty($line['items']))
            ->values();

        if ($lines->isEmpty()) {
            throw ValidationException::withMessages([
                'lines' => [__('validation.required', ['attribute' => 'lines'])],
            ]);
        }

        /** @var BookingOrder $order */
        $order = DB::transaction(function () use ($client, $payload, $lines): BookingOrder {
            /** @var BookingOrder $order */
            $order = BookingOrder::query()->create([
                'client_id' => $client->id,
                'source' => $payload['source'] ?? 'site',
                'comment' => $payload['comment'] ?? null,
                'status' => AppointmentStatus::Pending->value,
            ]);

            foreach ($lines as $line) {
                $startAt = (string) $line['start_at'];
                $items = collect((array) $line['items']);

                $explicitMasterIds = $items
                    ->pluck('master_id')
                    ->filter(fn (mixed $value): bool => $value !== null && (int) $value > 0)
                    ->unique()
                    ->values();

                if ($explicitMasterIds->count() === 1) {
                    $masterId = (int) $explicitMasterIds->first();
                    $serviceIds = $items
                        ->pluck('service_id')
                        ->map(fn (mixed $value): int => (int) $value)
                        ->filter(fn (int $id): bool => $id > 0)
                        ->unique()
                        ->values()
                        ->all();

                    $this->create($client, [
                        'booking_order_id' => $order->id,
                        'master_id' => $masterId,
                        'service_ids' => $serviceIds,
                        'start_at' => $startAt,
                        'comment' => $payload['comment'] ?? null,
                        'source' => $payload['source'] ?? 'site',
                    ]);
                    continue;
                }

                if ($explicitMasterIds->count() > 1) {
                    throw ValidationException::withMessages([
                        'lines' => [__('validation.in', ['attribute' => 'master_id'])],
                    ]);
                }

                $this->createMany($client, [
                    'booking_order_id' => $order->id,
                    'items' => $items->all(),
                    'start_at' => $startAt,
                    'comment' => $payload['comment'] ?? null,
                    'source' => $payload['source'] ?? 'site',
                ]);
            }

            $order->refreshAggregates();
            $order->save();

            return $order;
        });

        return $order;
    }

    public function resolveGuestClient(string $guestName, string $guestPhone): User
    {
        $normalizedPhone = $this->normalizePhone($guestPhone);

        /** @var User|null $existing */
        $existing = User::query()
            ->where('phone', $normalizedPhone)
            ->first();

        if ($existing !== null) {
            if (! $existing->hasRole('client')) {
                $existing->assignRole('client');
            }

            return $existing;
        }

        $phoneToken = preg_replace('/\D+/', '', $normalizedPhone) ?: Str::lower(Str::random(8));
        $guestEmail = sprintf('guest-%s-%s@example.local', $phoneToken, Str::lower(Str::random(6)));

        /** @var User $client */
        $client = User::query()->create([
            'name' => trim($guestName),
            'phone' => $normalizedPhone,
            'email' => $guestEmail,
            'password' => Str::random(32),
        ]);

        $client->assignRole('client');

        return $client;
    }

    public function cancelByClient(Appointment $appointment): Appointment
    {
        $from = $appointment->status->value;
        $to = AppointmentStatus::Cancelled->value;

        if (! AppointmentStatus::canTransition($from, $to)) {
            throw ValidationException::withMessages([
                'status' => [__('messages.appointment.transition_forbidden', ['from' => $from, 'to' => $to])],
            ]);
        }

        $appointment->status = AppointmentStatus::Cancelled;
        $appointment->save();

        return $appointment;
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ($digits === '') {
            return trim($phone);
        }

        return '+'.$digits;
    }
}
