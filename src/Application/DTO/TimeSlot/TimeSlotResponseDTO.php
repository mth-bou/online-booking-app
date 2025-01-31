<?php

namespace App\Application\DTO\TimeSlot;

use App\Domain\Model\TimeSlot;
use OpenApi\Attributes as OA;

class TimeSlotResponseDTO
{
    #[OA\Property(type: "integer", example: 1)]
    public int $id;

    #[OA\Property(type: "integer", example: 1)]
    public int $restaurantId;

    #[OA\Property(type: "string", format: "date-time", example: "2025-02-01 19:00:00")]
    public string $startTime;

    #[OA\Property(type: "string", format: "date-time", example: "2025-02-01 21:00:00")]
    public string $endTime;

    #[OA\Property(type: "boolean", example: true)]
    public bool $isAvailable;

    public function __construct(TimeSlot $timeSlot)
    {
        $this->id = $timeSlot->getId();
        $this->restaurantId = $timeSlot->getRestaurant()->getId();
        $this->startTime = $timeSlot->getStartTime()->format('Y-m-d H:i');
        $this->endTime = $timeSlot->getEndTime()->format('Y-m-d H:i');
        $this->isAvailable = $timeSlot->isAvailable();
    }
}