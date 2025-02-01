<?php

namespace App\Domain\Enum;

enum NotificationStatusEnum: string
{
    case PENDING = 'PENDING';
    case SENT = 'SENT';
    case FAILED = 'FAILED';

    public static function casesAsArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $method): bool
    {
        return in_array($method, self::casesAsArray(), true);
    }
}