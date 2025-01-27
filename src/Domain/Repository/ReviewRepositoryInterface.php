<?php

namespace App\Domain\Repository;

use App\Domain\Model\Review;

interface ReviewRepositoryInterface
{
    public function findById(int $id): ?Review;
    public function findByUser(int $userId): array;
    public function findByRestaurant(int $restaurantId): array;
    public function findByRating(int $rating): array;
    public function findRecentReviews(int $restaurantId, int $limit = 10): array;
    public function getAverageRating(int $restaurantId): ?float;
    public function save(Review $review): void;
    public function delete(Review $review): void;
}
