<?php

namespace App\Domain\Model\Interface;

use DateTimeImmutable;
use App\Domain\Model\Interface\UserInterface;
use App\Domain\Model\Interface\RestaurantInterface;

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

    public function getUser(): ?UserInterface;
    public function setUser(?UserInterface $user): static;

    public function getRestaurant(): ?RestaurantInterface;
    public function setRestaurant(?RestaurantInterface $restaurant): static;
}
