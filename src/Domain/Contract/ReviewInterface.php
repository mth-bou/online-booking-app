<?php

namespace App\Domain\Contract;

use DateTimeImmutable;
use App\Domain\Contract\UserModelInterface;
use App\Domain\Contract\RestaurantInterface;

interface ReviewInterface
{
    public function getId(): ?int;

    public function getRating(): ?int;
    public function setRating(int $rating): static;

    public function getComment(): ?string;
    public function setComment(?string $comment): static;

    public function getCreatedAt(): ?DateTimeImmutable;
    public function setCreatedAt(DateTimeImmutable $createdAt): static;

    public function getUpdatedAt(): ?DateTimeImmutable;
    public function setUpdatedAt(DateTimeImmutable $updatedAt): static;

    public function getUser(): ?UserModelInterface;
    public function setUser(?UserModelInterface $user): static;

    public function getRestaurant(): ?RestaurantInterface;
    public function setRestaurant(?RestaurantInterface $restaurant): static;
}
