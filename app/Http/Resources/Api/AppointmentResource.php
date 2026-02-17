<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'source' => $this->source,
            'comment' => $this->comment,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'service' => new ServiceResource($this->whenLoaded('service')),
            'master' => new MasterResource($this->whenLoaded('master')),
            'branch' => new BranchResource($this->whenLoaded('branch')),
            'client' => new UserResource($this->whenLoaded('client')),
            'created_at' => $this->created_at,
        ];
    }
}
