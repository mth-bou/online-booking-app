<?php

namespace App\Domain\Repository;

use App\Domain\Model\RestaurantInterface;

interface RestaurantRepositoryInterface
{
    public function createNew(): RestaurantInterface;
    public function findById(int $id): ?RestaurantInterface;
    public function findByName(string $name): ?RestaurantInterface;
    public function findAll(): array;
    public function findByCity(string $city): array;
    public function findByMinimumCapacity(int $capacity): array;
    public function search(string $keyword): array;
    public function save(RestaurantInterface $restaurant): void;
    public function delete(RestaurantInterface $restaurant): void;
}
