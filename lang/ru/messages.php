<?php

declare(strict_types=1);

return [
    'auth' => [
        'invalid_credentials' => 'Неверные учетные данные.',
        'logged_out' => 'Вы вышли из системы.',
    ],
    'appointment' => [
        'slot_occupied' => 'Выбранный временной слот уже занят.',
        'transition_forbidden' => 'Переход из :from в :to запрещен.',
    ],
    'filament' => [
        'resources' => [
            'appointment' => ['singular' => 'Запись', 'plural' => 'Записи'],
            'branch' => ['singular' => 'Филиал', 'plural' => 'Филиалы'],
            'category' => ['singular' => 'Категория', 'plural' => 'Категории'],
            'master' => ['singular' => 'Мастер', 'plural' => 'Мастера'],
            'service' => ['singular' => 'Услуга', 'plural' => 'Услуги'],
            'user' => ['singular' => 'Пользователь', 'plural' => 'Пользователи'],
        ],
        'fields' => [
            'client' => 'Клиент',
            'master' => 'Мастер',
            'branch' => 'Филиал',
            'service' => 'Услуга',
            'category' => 'Категория',
            'duration' => 'Длительность',
            'linked_user' => 'Связанный пользователь',
            'roles' => 'Роли',
        ],
        'status' => [
            'pending' => 'Ожидает',
            'confirmed' => 'Подтверждена',
            'cancelled' => 'Отменена',
            'done' => 'Выполнена',
            'no_show' => 'Не пришел',
        ],
        'source' => [
            'site' => 'Сайт',
            'phone' => 'Телефон',
            'instagram' => 'Instagram',
        ],
        'actions' => [
            'add_day_rule' => 'Добавить правило дня',
            'confirm_selected' => 'Подтвердить выбранные',
            'cancel_selected' => 'Отменить выбранные',
        ],
        'widgets' => [
            'appointments_today' => 'Записей сегодня',
            'appointments_week' => 'Записей за неделю',
            'new_clients_week' => 'Новых клиентов за неделю',
            'top_service' => 'Топ-услуга',
            'na' => 'Н/Д',
        ],
    ],
];
