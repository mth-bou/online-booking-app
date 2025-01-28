<?php

namespace App\Domain\Repository;

use App\Domain\Model\TableInterface;
use DateTime;

interface TableRepositoryInterface
{
    public function createNew(): TableInterface;
    public function findById(int $id): ?TableInterface;
    public function findByRestaurant(int $restaurantId): array;
    public function findByMinimumCapacity(int $capacity): array;
    public function findAvailableTables(int $restaurantId, DateTime $startTime, DateTime $endTime, int $guests): array;
    public function findReservedTables(int $restaurantId, DateTime $startTime, DateTime $endTime): array;
    public function isTableAvailable(int $tableId, DateTime $startTime, DateTime $endTime): bool;
    public function save(TableInterface $table): void;
    public function delete(TableInterface $table): void;
}
