<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Slot;

use Illuminate\Foundation\Http\FormRequest;

class SlotsQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'master_id' => ['required', 'integer', 'exists:masters,id'],
            'date' => ['required', 'date_format:Y-m-d'],
        ];
    }
}
