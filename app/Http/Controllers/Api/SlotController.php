<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Salon\Services\SlotGenerationService;
use App\Domain\Salon\Models\Branch;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Slot\SlotsQueryRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class SlotController extends Controller
{
    public function __construct(private readonly SlotGenerationService $slotGenerationService)
    {
    }

    public function index(SlotsQueryRequest $request): JsonResponse
    {
        $branch = Branch::query()->findOrFail($request->integer('branch_id'));
        $service = Service::query()->findOrFail($request->integer('service_id'));
        $master = Master::query()->findOrFail($request->integer('master_id'));
        $date = Carbon::createFromFormat('Y-m-d', (string) $request->string('date'));

        $slots = $this->slotGenerationService->generate($branch, $master, $service, $date);

        return response()->json(['data' => $slots]);
    }
}
