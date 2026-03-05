<?php

declare(strict_types=1);

return [
    'auth' => [
        'invalid_credentials' => 'Invalid credentials.',
        'logged_out' => 'Logged out.',
    ],
    'appointment' => [
        'slot_occupied' => 'Selected time slot is already occupied.',
        'transition_forbidden' => 'Transition from :from to :to is forbidden.',
    ],
    'filament' => [
        'resources' => [
            'appointment' => ['singular' => 'Appointment', 'plural' => 'Appointments'],
            'branch' => ['singular' => 'Branch', 'plural' => 'Branches'],
            'category' => ['singular' => 'Category', 'plural' => 'Categories'],
            'master' => ['singular' => 'Master', 'plural' => 'Masters'],
            'service' => ['singular' => 'Service', 'plural' => 'Services'],
            'user' => ['singular' => 'User', 'plural' => 'Users'],
        ],
        'fields' => [
            'client' => 'Client',
            'master' => 'Master',
            'branch' => 'Branch',
            'service' => 'Service',
            'category' => 'Category',
            'duration' => 'Duration',
            'linked_user' => 'Linked user',
            'roles' => 'Roles',
        ],
        'status' => [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
            'done' => 'Done',
            'no_show' => 'No show',
        ],
        'source' => [
            'site' => 'Site',
            'phone' => 'Phone',
            'instagram' => 'Instagram',
        ],
        'actions' => [
            'add_day_rule' => 'Add day rule',
            'confirm_selected' => 'Confirm selected',
            'cancel_selected' => 'Cancel selected',
        ],
        'widgets' => [
            'appointments_today' => 'Appointments Today',
            'appointments_week' => 'Appointments This Week',
            'new_clients_week' => 'New Clients This Week',
            'top_service' => 'Top Service',
            'na' => 'N/A',
        ],
    ],
];
