<?php

use App\Controllers\AuthController;
use App\Controllers\TripController;

return [
    '/' => [AuthController::class, 'loginForm'],
    '/login' => [AuthController::class, 'login'],
    '/logout' => [AuthController::class, 'logout'],
    '/dashboard' => [TripController::class, 'dashboard'],
    // additional routes will be added here
];
