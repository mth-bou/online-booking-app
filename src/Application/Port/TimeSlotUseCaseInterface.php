<?php

namespace App\Application\Port;

use App\Domain\Model\TimeSlot;
use DateTimeImmutable;

interface TimeSlotUseCaseInterface
{
    public function addTimeSlot(int $restaurantId, DateTimeImmutable $startTime, DateTimeImmutable $endTime, bool $isAvailable): TimeSlot;

    public function findTimeSlotById(int $timeSlotId): ?TimeSlot;

    public function getAvailableTimeSlots(int $restaurantId): array;

    public function isTimeSlotAvailable(int $restaurantId, DateTimeImmutable $startTime, DateTimeImmutable $endTime): bool;

    public function updateTimeSlot(int $timeSlotId, ?DateTimeImmutable $startTime = null, ?DateTimeImmutable $endTime = null, ?bool $isAvailable = null): TimeSlot;

    public function deleteTimeSlot(int $timeSlotId): void;
}