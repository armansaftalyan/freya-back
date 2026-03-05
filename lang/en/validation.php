<?php

declare(strict_types=1);

return [
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute field must be a string.',
    'integer' => 'The :attribute field must be an integer.',
    'email' => 'The :attribute field must be a valid email address.',
    'date' => 'The :attribute field must be a valid date.',
    'date_format' => 'The :attribute field must match the format :format.',
    'after' => 'The :attribute field must be a date after :date.',
    'exists' => 'The selected :attribute is invalid.',
    'unique' => 'The :attribute has already been taken.',
    'in' => 'The selected :attribute is invalid.',
    'min' => [
        'string' => 'The :attribute field must be at least :min characters.',
    ],
    'max' => [
        'string' => 'The :attribute field must not be greater than :max characters.',
    ],
    'attributes' => [
        'name' => 'name',
        'email' => 'email',
        'phone' => 'phone',
        'password' => 'password',
        'master_id' => 'master',
        'branch_id' => 'branch',
        'service_id' => 'service',
        'start_at' => 'start time',
        'comment' => 'comment',
        'source' => 'source',
        'date' => 'date',
    ],
];
