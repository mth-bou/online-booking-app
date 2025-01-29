<?php

namespace App\Application\DTO\Review;

use Symfony\Component\Validator\Constraints as Assert;

class ReviewRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $userId;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $restaurantId;

    #[Assert\NotBlank]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: "Rating should be between 1 and 5."
    )]
    public int $rating;

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