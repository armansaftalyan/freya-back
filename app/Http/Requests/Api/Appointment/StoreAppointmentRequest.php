<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'guest_phone' => $this->normalizePhone($this->input('guest_phone')),
        ]);

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

        if ($this->has('lines') && is_array($this->input('lines'))) {
            $normalizedLines = collect((array) $this->input('lines'))
                ->map(function ($line) {
                    if (! is_array($line)) {
                        return $line;
                    }

                    $masterId = isset($line['master_id']) ? (int) $line['master_id'] : null;

                    if (! isset($line['items']) && isset($line['service_ids']) && is_array($line['service_ids'])) {
                        $line['items'] = collect($line['service_ids'])
                            ->map(fn ($serviceId): array => [
                                'service_id' => (int) $serviceId,
                                'master_id' => $masterId,
                            ])
                            ->all();
                    }

                    if (isset($line['items']) && is_array($line['items']) && $masterId !== null) {
                        $line['items'] = collect($line['items'])
                            ->map(function ($item) use ($masterId): array {
                                $item = is_array($item) ? $item : [];
                                if (! array_key_exists('master_id', $item)) {
                                    $item['master_id'] = $masterId;
                                }
                                return $item;
                            })
                            ->all();
                    }

                    return $line;
                })
                ->all();

            $this->merge([
                'lines' => $normalizedLines,
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
            'lines' => ['nullable', 'array', 'min:1'],
            'lines.*.start_at' => ['required_with:lines', 'date', 'after:now'],
            'lines.*.items' => ['required_with:lines', 'array', 'min:1'],
            'lines.*.items.*.service_id' => ['required_with:lines.*.items', 'integer', 'exists:services,id'],
            'lines.*.items.*.master_id' => ['nullable', 'integer', 'exists:masters,id'],
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.service_id' => ['required_with:items', 'integer', 'exists:services,id'],
            'items.*.master_id' => ['nullable', 'integer', 'exists:masters,id'],
            'master_id' => [Rule::requiredIf(! $this->filled('items') && ! $this->filled('lines')), 'nullable', 'integer', 'exists:masters,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'service_ids' => [Rule::requiredIf(! $this->filled('items') && ! $this->filled('service_id') && ! $this->filled('lines')), 'nullable', 'array', 'min:1'],
            'service_ids.*' => ['required', 'integer', 'exists:services,id'],
            'start_at' => [Rule::requiredIf(! $this->filled('lines')), 'nullable', 'date', 'after:now'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', Rule::in(['site', 'phone', 'instagram', 'yandex_maps'])],
            'guest_name' => [Rule::requiredIf($this->user() === null), 'nullable', 'string', 'max:255'],
            'guest_phone' => [Rule::requiredIf($this->user() === null), 'nullable', 'string', 'max:32', 'regex:/^\+[1-9]\d{7,14}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_at.after' => __('messages.appointment.start_in_future'),
            'guest_phone.regex' => __('messages.auth.invalid_phone'),
        ];
    }

    private function normalizePhone(mixed $phone): ?string
    {
        if (! is_string($phone)) {
            return null;
        }

        $trimmed = trim($phone);
        if ($trimmed === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $trimmed) ?? '';
        if ($digits === '') {
            return $trimmed;
        }

        return '+'.$digits;
    }
}
