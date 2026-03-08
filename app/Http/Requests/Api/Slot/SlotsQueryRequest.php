<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Slot;

use Illuminate\Foundation\Http\FormRequest;

class SlotsQueryRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('service_id') && ! $this->has('service_ids')) {
            $this->merge([
                'service_ids' => [(int) $this->input('service_id')],
            ]);
        }

        if ($this->has('service_ids') && ! is_array($this->input('service_ids'))) {
            $this->merge([
                'service_ids' => [(int) $this->input('service_ids')],
            ]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'service_ids' => ['required_without:service_id', 'array', 'min:1'],
            'service_ids.*' => ['required', 'integer', 'exists:services,id'],
            'master_id' => ['required', 'integer', 'exists:masters,id'],
            'date' => ['required', 'date_format:Y-m-d'],
        ];
    }
}
