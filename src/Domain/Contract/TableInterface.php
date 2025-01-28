<?php

namespace App\Domain\Contract;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
interface TableInterface
{
    public function getId(): ?int;
    public function getTableNumber(): ?int;
    public function setTableNumber(int $tableNumber): static;
    public function getCapacity(): ?int;
    public function setCapacity(int $capacity): static;
    public function getRestaurant(): ?RestaurantInterface;
    public function setRestaurant(?RestaurantInterface $restaurant): static;
    public function getReservations(): Collection;
    public function addReservation(ReservationInterface $reservation): static;
    public function removeReservation(ReservationInterface $reservation): static;
}