<?php

namespace App\Services;

class LoggerService
{

    public static function isAllowed(string $method): bool
    {
        static $valid = null;
        $valid ??= config('logger.valid_functions');
        return isset($valid[$method]);
    }
}
