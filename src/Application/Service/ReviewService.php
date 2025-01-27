<?php

namespace App\Application\Service;

use App\Domain\Model\Review;
use App\Domain\Repository\ReviewRepositoryInterface;
use App\Domain\Repository\RestaurantRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Exception;

class ReviewService
{
    private ReviewRepositoryInterface $reviewRepository;
    private RestaurantRepositoryInterface $restaurantRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        ReviewRepositoryInterface $reviewRepository,
        RestaurantRepositoryInterface $restaurantRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->restaurantRepository = $restaurantRepository;
        $this->userRepository = $userRepository;
    }

    public function addReview(int $userId, int $restaurantId, int $rating, ?string $comment): Review
    {
        $user = $this->userRepository->findById($userId);
        $restaurant = $this->restaurantRepository->findById($restaurantId);

        if (!$user || !$restaurant) {
            throw new Exception("User or Restaurant not found.");
        }

        $review = new Review();
        $review->setUser($user);
        $review->setRestaurant($restaurant);
        $review->setRating($rating);
        $review->setComment($comment);
        $review->setCreatedAt(new DateTimeImmutable());

        $this->reviewRepository->save($review);

        return $review;
    }

    public function getRestaurantReviews(int $restaurantId): array
    {
        return $this->reviewRepository->findByRestaurant($restaurantId);
    }

    public function getUserReviews(int $userId): array
    {
        return $this->reviewRepository->findByUser($userId);
    }

    public function getAverageRating(int $restaurantId): ?float
    {
        return $this->reviewRepository->getAverageRating($restaurantId);
    }
}