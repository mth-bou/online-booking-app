<?php

namespace App\Application\Port;

use App\Domain\Enum\PaymentMethodEnum;
use App\Domain\Model\Payment;

interface PaymentUseCaseInterface
{
    public function processPayment(int $reservationId, float $amount, PaymentMethodEnum $paymentMethod): Payment;

    public function confirmPayment(int $paymentId): void;

    public function refundPayment(int $paymentId): void;
}