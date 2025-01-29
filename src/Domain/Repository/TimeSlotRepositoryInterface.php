<?php

namespace App\Domain\Repository;

use App\Domain\Model\TimeSlot;
use DateTime;
use DateTimeImmutable;

interface TimeSlotRepositoryInterface
{
    public function createNew(): TimeSlot;
    public function findById(int $id): ?TimeSlot;
    public function findAvailableByRestaurant(int $restaurantId): array;
    public function findByTable(int $tableId): array;
    public function findByDate(int $restaurantId, DateTime $date): array;
    public function isTimeSlotAvailable(int $tableId, DateTimeImmutable $startTime, DateTimeImmutable $endTime): bool;
    public function save(TimeSlot $timeSlot): void;
    public function delete(TimeSlot $timeSlot): void;
}
