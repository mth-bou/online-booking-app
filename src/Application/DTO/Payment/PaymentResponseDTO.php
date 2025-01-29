<?php

namespace App\Application\DTO\Payment;

use App\Domain\Model\Payment;
use DateTimeImmutable;

class PaymentResponseDTO
{
    public int $id;
    public int $reservationId;
    public float $amount;
    public string $paymentMethod;
    public string $status;
    public string $paymentDate;
    public DateTimeImmutable $createdAt;
    public DateTimeImmutable $updatedAt;

    public function __construct(Payment $payment)
    {
        $this->id = $payment->getId();
        $this->reservationId = $payment->getReservation()->getId();
        $this->amount = $payment->getAmount();
        $this->paymentMethod = $payment->getPaymentMethod();
        $this->status = $payment->getStatus();
        $this->paymentDate = $payment->getPaymentDate()->format('Y-m-d H:i:s');
        $this->createdAt = $payment->getCreatedAt();
        $this->updatedAt = $payment->getUpdatedAt();
    }
}