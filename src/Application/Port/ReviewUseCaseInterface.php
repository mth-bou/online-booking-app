<?php

namespace App\Application\Port;

use App\Domain\Model\Review;

interface ReviewUseCaseInterface
{
    public function addReview(int $userId, int $restaurantId, int $rating, ?string $comment): Review;

    public function getRestaurantReviews(int $restaurantId): array;

    public function getUserReviews(int $userId): array;

    public function getAverageRating(int $restaurantId): ?float;
}