<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

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

    'teams' => [
        'client_id' => env('MICROSOFT_TEAMS_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_TEAMS_CLIENT_SECRET'),
        'tenant_id' => env('MICROSOFT_TEAMS_TENANT_ID'),
        'fallback_email' => env('MICROSOFT_TEAMS_FALLBACK_EMAIL'),
    ],
    
    'agora' => [
        'app_id' => env('AGORA_APP_ID'),
        'app_certificate' => env('AGORA_APP_CERTIFICATE'),
        'customer_id' => env('AGORA_CUSTOMER_ID'),
        'customer_secret' => env('AGORA_CUSTOMER_SECRET'),
    ],

    'zoho' => [
        'client_id' => env('ZOHO_CRM_CLIENT_ID'),
        'client_secret' => env('ZOHO_CRM_CLIENT_SECRET'),
        'refresh_token' => env('ZOHO_CRM_REFRESH_TOKEN'),
        'region' => env('ZOHO_CRM_REGION', 'in'),
    ],

];
