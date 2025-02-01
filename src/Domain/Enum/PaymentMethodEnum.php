<?php

namespace App\Domain\Enum;

enum PaymentMethodEnum: string
{
    case CREDIT_CARD = 'CREDIT_CARD';
    case PAYPAL = 'PAYPAL';
    case BANK_TRANSFER = 'BANK_TRANSFER';

    public static function casesAsArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $method): bool
    {
        return in_array($method, self::casesAsArray(), true);
    }
}