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
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'api_keys' => env('GEMINI_API_KEYS', ''),
        'model' => env('GEMINI_MODEL', 'gemini-2.0-flash'),
        'timeout' => (int) env('GEMINI_TIMEOUT', 20),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'prompt_profile' => env('GEMINI_PROMPT_PROFILE', 'default'),
        'max_sentences' => (int) env('GEMINI_PROMPT_MAX_SENTENCES', 4),
    ],

    'fpt' => [
        'tts_api_key' => env('FPT_TTS_API_KEY'),
        'tts_voice' => env('FPT_TTS_VOICE', 'banmai'),
        'tts_speed' => (int) env('FPT_TTS_SPEED', 0),
        'tts_format' => env('FPT_TTS_FORMAT', 'mp3'),
    ],

    'recaptcha' => [
        'site' => env('RECAPTCHA_SITE_KEY'),
        'secret' => env('RECAPTCHA_SECRET_KEY'),
    ],

    'python_ai' => [
        'url' => env('PYTHON_AI_URL', 'http://127.0.0.1:8001'),
    ],

];
