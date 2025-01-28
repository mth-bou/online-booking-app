<?php

namespace App\Domain\Contract;

use App\Domain\Model\Payment;
use App\Domain\Model\Table;
use App\Domain\Model\TimeSlot;
use App\Domain\Model\User;
use Doctrine\Common\Collections\Collection;
use DateTimeImmutable;
use DateTimeInterface;

interface ReservationInterface
{
    public function getId(): ?int;
    public function getUser(): ?User;
    public function setUser(?User $user): static;
    public function getDate(): ?DateTimeInterface;
    public function setDate(DateTimeInterface $date): static;
    public function getStatus(): ?string;
    public function setStatus(string $status): static;
    public function getCreatedAt(): ?DateTimeImmutable;
    public function getUpdatedAt(): ?DateTimeImmutable;
    public function setUpdatedAt(DateTimeImmutable $updatedAt): static;
    public function getTimeSlot(): ?TimeSlot;
    public function setTimeSlot(?TimeSlot $timeSlot): static;
    public function getTable(): ?Table;
    public function setTable(?Table $restaurantTable): static;
    public function getPayments(): Collection;
    public function addPayment(Payment $payment): static;
    public function removePayment(Payment $payment): static;
}