<?php

namespace App\Domain\Repository;

use App\Domain\Model\TimeSlot;
use DateTime;

interface TimeSlotRepositoryInterface
{
    public function findById(int $id): ?TimeSlot;
    public function findAvailableByRestaurant(int $restaurantId): array;
    public function findByTable(int $tableId): array;
    public function findByDate(int $restaurantId, DateTime $date): array;
    public function isTimeSlotAvailable(int $tableId, DateTime $startTime, DateTime $endTime): bool;
    public function save(TimeSlot $timeSlot): void;
    public function delete(TimeSlot $timeSlot): void;
}
