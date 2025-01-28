<?php

namespace App\Domain\Contract;

use DateTimeImmutable;
use App\Domain\Model\Restaurant;
use App\Domain\Model\User;

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

    public function getUser(): ?User;
    public function setUser(?User $user): static;

    public function getRestaurant(): ?Restaurant;
    public function setRestaurant(?Restaurant $restaurant): static;
}
