<?php

namespace App\Domain\Enum;

enum PaymentMethodEnum: string
{
    case CREDIT_CARD = 'credit_card';
    case PAYPAL = 'paypal';
    case BANK_TRANSFER = 'bank_transfer';

    public static function casesAsArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function isValid(string $method): bool
    {
        return in_array($method, self::casesAsArray(), true);
    }
}