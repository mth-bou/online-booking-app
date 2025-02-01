<?php

namespace App\Domain\Enum;

enum StatusEnum: string
{
    case PENDING = 'PENDING';
    case FAILED = 'FAILED';
    case CANCELED = 'CANCELED';
    case COMPLETED = 'COMPLETED';
    case REFUNDED = 'REFUNDED';
    case REJECTED = 'REJECTED';

    public static function isValid(string $status): bool
    {
        return in_array($status, self::casesAsArray(), true);
    }

    public static function casesAsArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}