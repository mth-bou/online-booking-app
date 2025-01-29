<?php

namespace App\Application\DTO\Review;

use App\Domain\Model\Review;

class ReviewResponseDTO
{
    public int $id;
    public int $userId;
    public string $firstname;
    public string $lastname;
    public int $restaurantId;
    public string $restaurantName;
    public int $rating;
    public ?string $comment;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(Review $review)
    {
        $this->id = $review->getId();
        $this->userId = $review->getUser()->getId();
        $this->firstname = $review->getUser()->getFirstname();
        $this->lastname = $review->getUser()->getLastname();
        $this->restaurantId = $review->getRestaurant()->getId();
        $this->restaurantName = $review->getRestaurant()->getName();
        $this->rating = $review->getRating();
        $this->comment = $review->getComment();
        $this->createdAt = $review->getCreatedAt()->format('Y-m-d H:i:s');
        $this->updatedAt = $review->getUpdatedAt()->format('Y-m-d H:i:s');
    }
}