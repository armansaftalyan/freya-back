<?php

declare(strict_types=1);

return [
    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'telegram' => [
        'webhook_url' => env('TELEGRAM_WEBHOOK_URL'),
    ],

    'appointments' => [
        'send_email_to_client' => (bool) env('APPOINTMENT_SEND_EMAIL_TO_CLIENT', false),
    ],

    'gift_cards' => [
        'webhook_token' => env('GIFT_CARD_WEBHOOK_TOKEN'),
        'auto_mark_paid' => (bool) env('GIFT_CARD_AUTO_MARK_PAID', false),
    ],

    'idram' => [
        'action_url' => env('IDRAM_ACTION_URL', 'https://banking.idram.am/Payment/GetPayment'),
        'rec_account' => env('IDRAM_REC_ACCOUNT'),
        'secret_key' => env('IDRAM_SECRET_KEY'),
        'test_force_amount' => env('IDRAM_TEST_FORCE_AMOUNT'),
    ],
];
