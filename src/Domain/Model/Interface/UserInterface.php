<?php

namespace App\Domain\Model\Interface;

use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends SymfonyUserInterface
{
    public function getId(): ?int;
    public function getEmail(): ?string;
    public function setEmail(string $email): static;
    public function getPassword(): ?string;
    public function setPassword(string $password): static;
    public function getFirstname(): ?string;
    public function setFirstname(?string $firstname): static;
    public function getLastname(): ?string;
    public function setLastname(?string $lastname): static;
    public function getPhoneNumber(): ?string;
    public function setPhoneNumber(?string $phoneNumber): static;
    public function getRoles(): array;
    public function setRoles(array $roles): static;
    public function getCreatedAt(): ?DateTimeImmutable;
    public function getUpdatedAt(): ?DateTimeImmutable;
    public function getReservations(): Collection;
    public function getNotifications(): Collection;
    public function getReviews(): Collection;
}