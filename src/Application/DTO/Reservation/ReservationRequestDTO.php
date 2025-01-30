<?php

namespace App\Application\DTO\Reservation;

use App\Domain\Enum\StatusEnum;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class ReservationRequestDTO
{
    #[OA\Property(type: "integer", example: 1)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $userId;

    #[OA\Property(type: "integer", example: 10)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $tableId;

    #[OA\Property(type: "integer", example: 5)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $timeSlotId;

    #[OA\Property(type: "string", example: "CONFIRMED")]
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