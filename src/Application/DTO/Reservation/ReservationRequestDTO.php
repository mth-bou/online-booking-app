<?php

namespace App\Application\DTO\Reservation;

use Symfony\Component\Validator\Constraints as Assert;

class ReservationRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $userId;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $tableId;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $timeSlotId;

    public function __construct(int $userId, int $tableId, int $timeSlotId)
    {
        $this->userId = $userId;
        $this->tableId = $tableId;
        $this->timeSlotId = $timeSlotId;
    }
}