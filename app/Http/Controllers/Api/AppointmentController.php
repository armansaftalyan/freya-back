<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Salon\Services\AppointmentService;
use App\Domain\Salon\Models\Appointment;
use App\Domain\Users\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Appointment\StoreAppointmentRequest;
use App\Http\Resources\Api\AppointmentResource;
use App\Http\Resources\Api\BookingOrderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentService $appointmentService)
    {
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        /** @var User|null $authenticatedUser */
        $authenticatedUser = $request->user();
        $payload = $request->validated();

        if ($authenticatedUser !== null) {
            $this->authorize('create', Appointment::class);
            $client = $authenticatedUser;
        } else {
            $client = $this->appointmentService->resolveGuestClient(
                (string) $payload['guest_name'],
                (string) $payload['guest_phone'],
            );
        }

        if (! empty($payload['items']) && is_array($payload['items'])) {
            $appointments = $this->appointmentService->createMany($client, $payload);
            $first = $appointments[0] ?? null;

            return response()->json([
                'data' => $first ? new AppointmentResource($first->load(['service', 'services', 'master', 'client'])) : null,
                'appointments' => AppointmentResource::collection(
                    collect($appointments)->map(
                        fn (Appointment $item): Appointment => $item->load(['service', 'services', 'master', 'client'])
                    )
                ),
            ], 201);
        }

        if (! empty($payload['lines']) && is_array($payload['lines'])) {
            $order = $this->appointmentService->createOrder($client, $payload)
                ->load([
                    'client',
                    'appointments.service',
                    'appointments.services',
                    'appointments.master',
                    'appointments.client',
                ]);
            /** @var \Illuminate\Support\Collection<int, Appointment> $appointments */
            $appointments = $order->appointments;
            $first = $appointments->first();

            return response()->json([
                'data' => $first ? new AppointmentResource($first) : null,
                'booking_order' => new BookingOrderResource($order),
                'appointments' => AppointmentResource::collection($appointments),
            ], 201);
        }

        $appointment = $this->appointmentService->create($client, $payload);

        return response()->json([
            'data' => new AppointmentResource($appointment->load(['service', 'services', 'master', 'client'])),
        ], 201);
    }

    public function my(Request $request): AnonymousResourceCollection
    {
        return AppointmentResource::collection(
            Appointment::query()
                ->with(['service', 'services', 'master'])
                ->where('client_id', $request->user()->id)
                ->latest('start_at')
                ->get()
        );
    }

    public function cancel(Request $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('cancel', $appointment);

        $appointment = $this->appointmentService->cancelByClient($appointment);

        return response()->json([
            'data' => new AppointmentResource($appointment->load(['service', 'services', 'master', 'client'])),
        ]);
    }
}
