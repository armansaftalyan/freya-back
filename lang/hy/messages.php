<?php

declare(strict_types=1);

return [
    'auth' => [
        'invalid_credentials' => 'Սխալ մուտքային տվյալներ։',
        'logged_out' => 'Դուք դուրս եկաք համակարգից։',
    ],
    'appointment' => [
        'slot_occupied' => 'Ընտրված ժամը արդեն զբաղված է։',
        'transition_forbidden' => ':from-ից :to անցումը արգելված է։',
    ],
    'filament' => [
        'resources' => [
            'appointment' => ['singular' => 'Գրանցում', 'plural' => 'Գրանցումներ'],
            'branch' => ['singular' => 'Մասնաճյուղ', 'plural' => 'Մասնաճյուղեր'],
            'category' => ['singular' => 'Կատեգորիա', 'plural' => 'Կատեգորիաներ'],
            'master' => ['singular' => 'Վարպետ', 'plural' => 'Վարպետներ'],
            'service' => ['singular' => 'Ծառայություն', 'plural' => 'Ծառայություններ'],
            'user' => ['singular' => 'Օգտատեր', 'plural' => 'Օգտատերեր'],
        ],
        'fields' => [
            'client' => 'Հաճախորդ',
            'master' => 'Վարպետ',
            'branch' => 'Մասնաճյուղ',
            'service' => 'Ծառայություն',
            'category' => 'Կատեգորիա',
            'duration' => 'Տևողություն',
            'linked_user' => 'Կցված օգտատեր',
            'roles' => 'Դերեր',
        ],
        'status' => [
            'pending' => 'Սպասման մեջ',
            'confirmed' => 'Հաստատված',
            'cancelled' => 'Չեղարկված',
            'done' => 'Ավարտված',
            'no_show' => 'Չներկայացավ',
        ],
        'source' => [
            'site' => 'Կայք',
            'phone' => 'Հեռախոս',
            'instagram' => 'Instagram',
        ],
        'actions' => [
            'add_day_rule' => 'Ավելացնել օրվա կանոն',
            'confirm_selected' => 'Հաստատել ընտրվածները',
            'cancel_selected' => 'Չեղարկել ընտրվածները',
        ],
        'widgets' => [
            'appointments_today' => 'Այսօրվա գրանցումներ',
            'appointments_week' => 'Շաբաթվա գրանցումներ',
            'new_clients_week' => 'Նոր հաճախորդներ շաբաթվա ընթացքում',
            'top_service' => 'Լավագույն ծառայություն',
            'na' => 'Չկա',
        ],
    ],
];
