<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'master_id' => ['required', 'integer', 'exists:masters,id'],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'start_at' => ['required', 'date', 'after:now'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', Rule::in(['site', 'phone', 'instagram'])],
        ];
    }
}
