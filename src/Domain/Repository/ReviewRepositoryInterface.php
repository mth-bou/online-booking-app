<?php

namespace App\Domain\Repository;

use App\Domain\Model\Interface\ReviewInterface;

interface ReviewRepositoryInterface
{
    public function createNew(): ReviewInterface;
    public function findById(int $id): ?ReviewInterface;
    public function findByUser(int $userId): array;
    public function findByRestaurant(int $restaurantId): array;
    public function findByRating(int $rating): array;
    public function findRecentReviews(int $restaurantId, int $limit = 10): array;
    public function getAverageRating(int $restaurantId): ?float;
    public function save(ReviewInterface $review): void;
    public function delete(ReviewInterface $review): void;
}
