<?php

/*
|--------------------------------------------------------------------------
| Authentication Module Configuration
|--------------------------------------------------------------------------
|
| This file keeps the portable Users Auth module pointed at the shared
| users database without forcing each POS API to duplicate auth logic.
|
*/

return [
    'users_connection' => env('AUTH_USERS_DB_CONNECTION'),
];
