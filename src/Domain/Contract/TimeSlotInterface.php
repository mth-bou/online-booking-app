<?php

namespace App\Domain\Contract;

use App\Domain\Model\Reservation;
use App\Domain\Model\Restaurant;
use Doctrine\Common\Collections\Collection;
use DateTimeImmutable;
use DateTimeInterface;

interface TimeSlotInterface
{
    public function getId(): ?int;
    public function getStartTime(): ?DateTimeInterface;
    public function setStartTime(DateTimeImmutable $startTime): static;
    public function getEndTime(): ?DateTimeInterface;
    public function setEndTime(DateTimeImmutable $endTime): static;
    public function isAvailable(): ?bool;
    public function setIsAvailable(bool $isAvailable): static;
    public function getCreatedAt(): ?DateTimeImmutable;
    public function setCreatedAt(DateTimeImmutable $createdAt): static;
    public function getUpdatedAt(): ?DateTimeImmutable;
    public function setUpdatedAt(DateTimeImmutable $updatedAt): static;
    public function getRestaurant(): ?Restaurant;
    public function setRestaurant(?Restaurant $restaurant): static;
    public function getReservations(): Collection;
    public function addReservation(Reservation $reservation): static;
    public function removeReservation(Reservation $reservation): static;
}