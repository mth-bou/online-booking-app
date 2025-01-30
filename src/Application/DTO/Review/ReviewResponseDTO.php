<?php

namespace App\Application\DTO\Review;

use App\Domain\Model\Review;
use OpenApi\Attributes as OA;

class ReviewResponseDTO
{
    #[OA\Property(type: "integer", example: 1)]
    public int $id;

    #[OA\Property(type: "integer", example: 1)]
    public int $userId;

    #[OA\Property(type: "string", example: "John")]
    public string $firstname;

    #[OA\Property(type: "string", example: "Doe")]
    public string $lastname;

    #[OA\Property(type: "integer", example: 10)]
    public int $restaurantId;

    #[OA\Property(type: "string", example: "Le Gourmet")]
    public string $restaurantName;

    #[OA\Property(type: "integer", example: 5)]
    public int $rating;

    #[OA\Property(type: "string", example: "Amazing food!", nullable: true)]
    public ?string $comment;

    #[OA\Property(type: "string", format: "date-time", example: "2023-10-15 14:23:00")]
    public string $createdAt;

    #[OA\Property(type: "string", format: "date-time", example: "2023-10-15 14:23:00")]
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