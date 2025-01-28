<?php

namespace App\Domain\Model\Interface;

use App\Domain\Model\Interface\UserInterface;
use DateTimeImmutable;

interface NotificationInterface
{
    public function getId(): ?int;
    public function getMessage(): ?string;
    public function setMessage(string $message): static;
    public function getType(): ?string;
    public function setType(string $type): static;
    public function getStatus(): ?string;
    public function setStatus(string $status): static;
    public function getIsRead(): bool;
    public function setIsRead(bool $isRead): static;
    public function getCreatedAt(): ?DateTimeImmutable;
    public function getUpdatedAt(): ?DateTimeImmutable;
    public function setUpdatedAt(DateTimeImmutable $updatedAt): static;
    public function getUser(): ?UserInterface;
    public function setUser(?UserInterface $user): static;
}