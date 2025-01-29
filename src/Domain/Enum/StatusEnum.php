<?php

namespace App\Domain\Enum;

enum StatusEnum: string
{
    case PENDING = 'PENDING';
    case SENT = 'SENT';
    case FAILED = 'FAILED';
    case CANCELED = 'CANCELED';
    case ARCHIVED = 'ARCHIVED';
    case COMPLETED = 'COMPLETED';
    case REFUNDED = 'REFUNDED';
    case REJECTED = 'REJECTED';
    case CONFIRMED = 'CONFIRMED';

    public static function isValid(string $status): bool
    {
        return in_array($status, self::casesAsArray(), true);
    }

    public static function casesAsArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}