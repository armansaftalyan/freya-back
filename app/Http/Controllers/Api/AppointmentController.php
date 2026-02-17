<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Salon\Services\AppointmentService;
use App\Domain\Salon\Models\Appointment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Appointment\StoreAppointmentRequest;
use App\Http\Resources\Api\AppointmentResource;
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
        $this->authorize('create', Appointment::class);

        $appointment = $this->appointmentService->create($request->user(), $request->validated());

        return response()->json([
            'data' => new AppointmentResource($appointment->load(['service', 'master', 'branch', 'client'])),
        ], 201);
    }

    public function my(Request $request): AnonymousResourceCollection
    {
        return AppointmentResource::collection(
            Appointment::query()
                ->with(['service', 'master', 'branch'])
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
            'data' => new AppointmentResource($appointment->load(['service', 'master', 'branch', 'client'])),
        ]);
    }
}
