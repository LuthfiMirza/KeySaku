<?php

return [
    'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),
    'bot_username' => env('TELEGRAM_BOT_USERNAME', 'Keysaku_bot'),
    'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET', ''),
    'api_base_url' => env('TELEGRAM_API_URL', 'https://api.telegram.org'),
];
