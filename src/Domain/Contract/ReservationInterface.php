<?php

namespace App\Domain\Contract;

use Doctrine\Common\Collections\Collection;
use DateTimeImmutable;
use DateTimeInterface;

interface ReservationInterface
{
    public function getId(): ?int;
    public function getUser(): ?UserModelInterface;
    public function setUser(?UserModelInterface $user): static;
    public function getDate(): ?DateTimeInterface;
    public function setDate(DateTimeInterface $date): static;
    public function getStatus(): ?string;
    public function setStatus(string $status): static;
    public function getCreatedAt(): ?DateTimeImmutable;
    public function getUpdatedAt(): ?DateTimeImmutable;
    public function setUpdatedAt(DateTimeImmutable $updatedAt): static;
    public function getTimeSlot(): ?TimeSlotInterface;
    public function setTimeSlot(?TimeSlotInterface $timeSlot): static;
    public function getTable(): ?TableInterface;
    public function setTable(?TableInterface $restaurantTable): static;
    public function getPayments(): Collection;
    public function addPayment(PaymentInterface $payment): static;
    public function removePayment(PaymentInterface $payment): static;
}