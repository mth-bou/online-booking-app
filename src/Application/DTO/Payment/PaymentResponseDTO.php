<?php

namespace App\Application\DTO\Payment;

use App\Domain\Model\Payment;

class PaymentResponseDTO
{
    public int $id;
    public int $reservationId;
    public float $amount;
    public string $paymentMethod;
    public string $status;
    public string $paymentDate;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(Payment $payment)
    {
        $this->id = $payment->getId();
        $this->reservationId = $payment->getReservation()->getId();
        $this->amount = $payment->getAmount();
        $this->paymentMethod = $payment->getPaymentMethod()->value; // Conversion en string
        $this->status = $payment->getStatus();
        $this->paymentDate = $payment->getPaymentDate()?->format('Y-m-d H:i:s') ?? 'N/A';
        $this->createdAt = $payment->getCreatedAt()->format('Y-m-d H:i:s');
        $this->updatedAt = $payment->getUpdatedAt()->format('Y-m-d H:i:s');
    }
}