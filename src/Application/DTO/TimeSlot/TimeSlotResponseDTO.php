<?php

namespace App\Application\DTO\TimeSlot;

use App\Domain\Model\TimeSlot;

class TimeSlotResponseDTO
{
    public int $id;
    public int $restaurantId;
    public string $restaurantName;
    public string $startTime;
    public string $endTime;
    public bool $isAvailable;

    public function __construct(TimeSlot $timeSlot)
    {
        $this->id = $timeSlot->getId();
        $this->restaurantId = $timeSlot->getRestaurant()->getId();
        $this->restaurantName = $timeSlot->getRestaurant()->getName();
        $this->startTime = $timeSlot->getStartTime()->format('Y-m-d H:i');
        $this->endTime = $timeSlot->getEndTime()->format('Y-m-d H:i');
        $this->isAvailable = $timeSlot->isAvailable();
    }
}