<?php

namespace App\Domain\Model\Interface;

use DateTimeImmutable;
use DateTimeInterface;

interface PaymentInterface
{
    public function getId(): ?int;
    public function getPaymentDate(): ?DateTimeInterface;
    public function setPaymentDate(DateTimeInterface $paymentDate): static;
    public function getAmount(): ?float;
    public function setAmount(float $amount): static;
    public function getStatus(): ?string;
    public function setStatus(string $status): static;
    public function getPaymentMethod(): ?string;
    public function setPaymentMethod(string $paymentMethod): static;
    public function getCreatedAt(): ?DateTimeImmutable;
    public function getUpdatedAt(): ?DateTimeImmutable;
    public function setUpdatedAt(DateTimeImmutable $updatedAt): static;
    public function getReservation(): ?Reservation;
    public function setReservation(?Reservation $reservation): static;
}
