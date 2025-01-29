<?php

namespace App\Application\DTO\Reservation;

use App\Domain\Model\Reservation;

class ReservationResponseDTO
{
    public int $id;
    public int $userId;
    public int $tableId;
    public int $timeSlotId;
    public string $status;
    public string $createdAt;
    public string $updatedAt;

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