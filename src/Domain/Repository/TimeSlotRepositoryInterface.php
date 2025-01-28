<?php

namespace App\Domain\Repository;

use App\Domain\Model\TimeSlotInterface;
use DateTime;
use DateTimeImmutable;

interface TimeSlotRepositoryInterface
{
    public function createNew(): TimeSlotInterface;
    public function findById(int $id): ?TimeSlotInterface;
    public function findAvailableByRestaurant(int $restaurantId): array;
    public function findByTable(int $tableId): array;
    public function findByDate(int $restaurantId, DateTime $date): array;
    public function isTimeSlotAvailable(int $tableId, DateTimeImmutable $startTime, DateTimeImmutable $endTime): bool;
    public function save(TimeSlotInterface $timeSlot): void;
    public function delete(TimeSlotInterface $timeSlot): void;
}
