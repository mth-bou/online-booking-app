<?php

namespace App\Domain\Model;

use App\Domain\Model\Table;
use App\Domain\Model\TimeSlot;
use Doctrine\Common\Collections\Collection;

interface RestaurantInterface
{
    public function getId(): ?int;

    public function getName(): ?string;
    public function setName(string $name): static;

    public function getType(): ?string;
    public function setType(string $type): static;

    public function getDescription(): ?string;
    public function setDescription(string $description): static;

    public function getAddress(): ?string;
    public function setAddress(string $address): static;

    public function getCity(): ?string;
    public function setCity(string $city): static;

    public function getPostalCode(): ?string;
    public function setPostalCode(string $postalCode): static;

    public function getPhoneNumber(): ?string;
    public function setPhoneNumber(string $phoneNumber): static;

    public function getTables(): Collection;
    public function addTable(Table $table): static;
    public function removeTable(Table $table): static;

    public function getReviews(): Collection;
    public function addReview(ReviewInterface $review): static;
    public function removeReview(ReviewInterface $review): static;

    public function getTimeSlots(): Collection;
    public function addTimeSlot(TimeSlot $timeSlot): static;
    public function removeTimeSlot(TimeSlot $timeSlot): static;
}