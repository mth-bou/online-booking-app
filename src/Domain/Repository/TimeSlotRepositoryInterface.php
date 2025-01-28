<?php

namespace App\Domain\Repository;

use App\Domain\Model\Interface\TimeSlotInterface;
use DateTime;

interface TimeSlotRepositoryInterface
{
    public function createNew(): TimeSlotInterface;
    public function findById(int $id): ?TimeSlotInterface;
    public function findAvailableByRestaurant(int $restaurantId): array;
    public function findByTable(int $tableId): array;
    public function findByDate(int $restaurantId, DateTime $date): array;
    public function isTimeSlotAvailable(int $tableId, DateTime $startTime, DateTime $endTime): bool;
    public function save(TimeSlotInterface $timeSlot): void;
    public function delete(TimeSlotInterface $timeSlot): void;
}
