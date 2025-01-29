<?php

namespace App\Application\DTO\Reservation;

use App\Domain\Enum\StatusEnum;
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

    #[Assert\Choice(callback: [StatusEnum::class, 'casesAsArray'], message: "Invalid status.")]
    public ?string $status;

    public function __construct(int $userId, int $tableId, int $timeSlotId, ?string $status)
    {
        $this->userId = $userId;
        $this->tableId = $tableId;
        $this->timeSlotId = $timeSlotId;
        $this->status = $status;
    }
}