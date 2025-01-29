<?php

namespace App\Application\Port;

use App\Domain\Model\Restaurant;
use App\Domain\Model\Table;

interface RestaurantUseCaseInterface
{
    // Gestion des restaurants
    public function createRestaurant(
        string $name, 
        string $type, 
        string $description, 
        string $address, 
        string $city, 
        string $postalCode, 
        string $phoneNumber
    ): Restaurant;

    public function updateRestaurant(int $restaurantId, array $data): Restaurant;

    public function deleteRestaurant(int $restaurantId): void;

    public function getRestaurantById(int $restaurantId): ?Restaurant;

    public function getRestaurantByName(string $name): ?Restaurant;

    public function getRestaurantsByCity(string $city): array;

    public function getRestaurantsByMinimumCapacity(int $capacity): array;

    public function searchRestaurants(string $keyword): array;

    public function getAllRestaurants(): array;

    // Gestion des tables
    public function getRestaurantTables(int $restaurantId): array;

    public function addTableToRestaurant(int $restaurantId, int $capacity, string $tableNumber): Table;

    public function removeTableFromRestaurant(int $tableId): void;

    // Gestion des avis et notation
    public function getRestaurantReviews(int $restaurantId): array;

    public function calculateAverageRating(int $restaurantId): ?float;
}