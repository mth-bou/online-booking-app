<?php

namespace App\Domain\Contract;

use App\Domain\Contract\RestaurantInterface;
use App\Domain\Contract\ReservationInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
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
    public function getRestaurant(): ?RestaurantInterface;
    public function setRestaurant(?RestaurantInterface $restaurant): static;
    public function getReservations(): Collection;
    public function addReservation(ReservationInterface $reservation): static;
    public function removeReservation(ReservationInterface $reservation): static;
}