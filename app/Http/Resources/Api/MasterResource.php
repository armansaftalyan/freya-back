<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'bio' => $this->bio,
            'avatar' => $this->avatar,
            'is_active' => $this->is_active,
            'sort' => $this->sort,
        ];
    }
}
