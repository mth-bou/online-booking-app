<?php

namespace App\Application\DTO\Review;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class ReviewRequestDTO
{
    #[OA\Property(type: "integer", example: 1)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $userId;

    #[OA\Property(type: "integer", example: 10)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $restaurantId;

    #[OA\Property(type: "integer", example: 5)]
    #[Assert\NotBlank]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: "Rating should be between 1 and 5."
    )]
    public int $rating;

    #[OA\Property(type: "string", example: "Amazing food!", nullable: true)]
    #[Assert\Length(max: 255)]
    public ?string $comment;

    public function __construct(int $userId, int $restaurantId, int $rating, ?string $comment = null)
    {
        $this->userId = $userId;
        $this->restaurantId = $restaurantId;
        $this->rating = $rating;
        $this->comment = $comment;
    }
}