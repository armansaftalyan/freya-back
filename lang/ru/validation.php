<?php

declare(strict_types=1);

return [
    'required' => 'Поле :attribute обязательно.',
    'string' => 'Поле :attribute должно быть строкой.',
    'integer' => 'Поле :attribute должно быть целым числом.',
    'email' => 'Поле :attribute должно быть корректным email-адресом.',
    'date' => 'Поле :attribute должно быть корректной датой.',
    'date_format' => 'Поле :attribute должно соответствовать формату :format.',
    'after' => 'Поле :attribute должно быть датой после :date.',
    'exists' => 'Выбранное значение поля :attribute некорректно.',
    'unique' => 'Поле :attribute уже занято.',
    'in' => 'Выбранное значение поля :attribute некорректно.',
    'min' => [
        'string' => 'Поле :attribute должно быть не короче :min символов.',
    ],
    'max' => [
        'string' => 'Поле :attribute не должно быть длиннее :max символов.',
    ],
    'attributes' => [
        'name' => 'имя',
        'email' => 'email',
        'phone' => 'телефон',
        'password' => 'пароль',
        'master_id' => 'мастер',
        'branch_id' => 'филиал',
        'service_id' => 'услуга',
        'start_at' => 'время начала',
        'comment' => 'комментарий',
        'source' => 'источник',
        'date' => 'дата',
    ],
];
