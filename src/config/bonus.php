<?php

return [
    'bonus_url' => env('BONUS_URL', 'http://bonus.local.com/api/test'),
    // STATUS options: SUCCESS => 1, PENDING => 2, NOT_VERIFIED => 3, CANCELED => 4
    // TYPE options: SHOP => 1, SITE => 2, EXTRA => 3
    'bonus_type' => env('BONUS_TYPE', 2),
    'bonus_site_id' => env('BONUS_SITE_ID', 1),
    'token' => env('BONUS_TOKEN', 'testtoken'),
];
