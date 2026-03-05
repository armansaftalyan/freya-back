<?php

declare(strict_types=1);

namespace App\Application\Salon\Services;

use App\Domain\Salon\Enums\AppointmentStatus;
use App\Domain\Salon\Models\Appointment;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use App\Domain\Users\Models\User;
use App\Infrastructure\Notifications\AppointmentNotifier;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function __construct(
        private readonly SlotGenerationService $slotGenerationService,
        private readonly AppointmentNotifier $appointmentNotifier,
    ) {
    }

    /** @param array{master_id:int,branch_id:int,service_id:int,start_at:string,comment?:string,source?:string} $payload */
    public function create(User $client, array $payload): Appointment
    {
        /** @var Service $service */
        $service = Service::query()->findOrFail($payload['service_id']);
        /** @var Master $master */
        $master = Master::query()->findOrFail($payload['master_id']);

        $startAt = Carbon::parse($payload['start_at']);
        $endAt = $startAt->copy()->addMinutes((int) $service->duration_minutes);

        if ($this->slotGenerationService->hasConflict($master->id, $startAt, $endAt)) {
            throw ValidationException::withMessages([
                'start_at' => [__('messages.appointment.slot_occupied')],
            ]);
        }

        /** @var Appointment $appointment */
        $appointment = Appointment::query()->create([
            'client_id' => $client->id,
            'master_id' => $master->id,
            'branch_id' => $payload['branch_id'],
            'service_id' => $service->id,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => AppointmentStatus::Pending,
            'comment' => $payload['comment'] ?? null,
            'source' => $payload['source'] ?? 'site',
        ]);

        $this->appointmentNotifier->notifyCreated($appointment);

        return $appointment;
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

        $guestEmail = sprintf('guest-%s-%s@example.local', $normalizedPhone, Str::lower(Str::random(6)));

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
        return trim(preg_replace('/\s+/', '', $phone) ?? $phone);
    }
}
