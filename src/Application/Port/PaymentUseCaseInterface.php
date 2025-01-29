<?php

namespace App\Application\Port;

use App\Domain\Model\Payment;

interface PaymentUseCaseInterface
{
    public function processPayment(int $reservationId, float $amount, string $paymentMethod): Payment;

    public function confirmPayment(int $paymentId): void;

    public function refundPayment(int $paymentId): void;
}