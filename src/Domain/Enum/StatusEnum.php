<?php

namespace App\Domain\Enum;

enum StatusEnum: string
{
    case PENDING = 'Pending';
    case SENT = 'Sent';
    case FAILED = 'Failed';
    case CANCELED = 'Canceled';
    case ARCHIVED = 'Archived';
    case COMPLETED = 'Completed';
    case REFUNDED = 'Refunded';
    case REJECTED = 'Rejected';
    case CONFIRMED = 'Confirmed';

    public static function isValid(string $status): bool
    {
        return in_array($status, self::casesAsArray(), true);
    }

    public static function casesAsArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}