<?php

namespace App\Application\DTO\Payment;

use App\Domain\Model\Payment;
use OpenApi\Attributes as OA;

#[OA\Schema]
class PaymentResponseDTO
{
    #[OA\Property(type: "integer", example: 1)]
    public int $id;

    #[OA\Property(type: "integer", example: 123)]
    public int $reservationId;

    #[OA\Property(type: "number", format: "float", example: 99.99)]
    public float $amount;

    #[OA\Property(type: "string", example: "credit_card")]
    public string $paymentMethod;

    #[OA\Property(type: "string", example: "confirmed")]
    public string $status;

    #[OA\Property(type: "string", format: "date-time", example: "2023-10-15 14:23:00")]
    public string $paymentDate;

    #[OA\Property(type: "string", format: "date-time", example: "2023-10-15 14:23:00")]
    public string $createdAt;

    #[OA\Property(type: "string", format: "date-time", example: "2023-10-15 14:23:00")]
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