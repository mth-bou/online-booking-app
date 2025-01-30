<?php

namespace App\Application\DTO\Reservation;

use App\Domain\Model\Reservation;
use OpenApi\Attributes as OA;

class ReservationResponseDTO
{
    #[OA\Property(type: "integer", example: 1)]
    public int $id;

    #[OA\Property(type: "integer", example: 1)]
    public int $userId;

    #[OA\Property(type: "integer", example: 10)]
    public int $tableId;

    #[OA\Property(type: "integer", example: 5)]
    public int $timeSlotId;

    #[OA\Property(type: "string", example: "confirmed")]
    public string $status;

    #[OA\Property(type: "string", format: "date-time", example: "2023-10-15 14:23:00")]
    public string $createdAt;

    #[OA\Property(type: "string", format: "date-time", example: "2023-10-15 14:23:00")]
    public string $updatedAt;

    #[OA\Property(
        type: "array",
        items: new OA\Items(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 101),
                new OA\Property(property: "amount", type: "number", format: "float", example: 99.99),
                new OA\Property(property: "paymentMethod", type: "string", example: "credit_card"),
                new OA\Property(property: "status", type: "string", example: "CONFIRMED"),
                new OA\Property(property: "createdAt", type: "string", format: "date-time", example: "2023-10-15 14:23:00"),
                new OA\Property(property: "updatedAt", type: "string", format: "date-time", example: "2023-10-15 14:23:00")
            ]
        )
    )]
    /** @var array<int, array<string, mixed>> */
    public array $payments = [];

    public function __construct(Reservation $reservation)
    {
        $this->id = $reservation->getId();
        $this->userId = $reservation->getUser()->getId();
        $this->tableId = $reservation->getTable()->getId();
        $this->timeSlotId = $reservation->getTimeSlot()->getId();
        $this->status = $reservation->getStatus();
        $this->createdAt = $reservation->getCreatedAt()->format('Y-m-d H:i:s');
        $this->updatedAt = $reservation->getUpdatedAt()->format('Y-m-d H:i:s');

        foreach ($reservation->getPayments() as $payment) {
            $this->payments[] = [
                'id' => $payment->getId(),
                'amount' => $payment->getAmount(),
                'paymentMethod' => $payment->getPaymentMethod(),
                'status' => $payment->getStatus(),
                'isConfirmed' => $payment->getStatus() === 'CONFIRMED',
                'paymentDate' => $payment->getPaymentDate()?->format('Y-m-d H:i:s'),
            ];
        }
    }
}