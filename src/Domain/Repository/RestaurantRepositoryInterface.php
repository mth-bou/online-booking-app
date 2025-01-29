<?php

namespace App\Domain\Repository;

use App\Domain\Model\Restaurant;

interface RestaurantRepositoryInterface
{
    public function createNew(): Restaurant;
    public function findById(int $id): ?Restaurant;
    public function findByName(string $name): ?Restaurant;
    public function findAll(): array;
    public function findByCity(string $city): array;
    public function findByMinimumCapacity(int $capacity): array;
    public function search(string $keyword): array;
    public function save(Restaurant $restaurant): void;
    public function delete(Restaurant $restaurant): void;
}
