<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Slot;

use Illuminate\Foundation\Http\FormRequest;

class ComboSlotsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => ['required', 'integer', 'exists:services,id'],
            'items.*.master_id' => ['nullable', 'integer', 'exists:masters,id'],
            'date' => ['required', 'date_format:Y-m-d'],
        ];
    }
}
