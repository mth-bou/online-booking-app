<?php

namespace App\Domain\Contract;

use App\Domain\Model\Reservation;
use App\Domain\Model\Restaurant;
use Doctrine\Common\Collections\Collection;

interface TableInterface
{
    public function getId(): ?int;
    public function getTableNumber(): ?int;
    public function setTableNumber(int $tableNumber): static;
    public function getCapacity(): ?int;
    public function setCapacity(int $capacity): static;
    public function getRestaurant(): ?Restaurant;
    public function setRestaurant(?Restaurant $restaurant): static;
    public function getReservations(): Collection;
    public function addReservation(Reservation $reservation): static;
    public function removeReservation(Reservation $reservation): static;
}