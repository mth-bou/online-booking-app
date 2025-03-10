<?php

namespace App\Domain\Model;

use App\Domain\Enum\StatusEnum;
use App\Domain\Model\Payment;
use App\Domain\Model\Table;
use App\Domain\Model\TimeSlot;
use App\Infrastructure\Persistence\Repository\ReservationRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'reservation',
    indexes: [
        new ORM\Index(name: 'IDX_RESERVATION_USER', columns: ['user_id'])
    ]
)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(callback: [StatusEnum::class, 'casesAsArray'], message: "Invalid Status.")]
    private ?string $status = StatusEnum::PENDING->value;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TimeSlot $timeSlot = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: true, name: 'restaurant_table_id')]
    private ?Table $restaurantTable = null;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'reservation')]
    private Collection $payments;

    #[ORM\ManyToOne(targetEntity: Restaurant::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant = null;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        if (!StatusEnum::isValid($status)) {
            throw new \InvalidArgumentException("Invalid status: " . $status);
        }
        
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PreUpdate]
    public function updateTimeStamps(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getTimeSlot(): ?TimeSlot
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(?TimeSlot $timeSlot): static
    {
        $this->timeSlot = $timeSlot;

        return $this;
    }

    public function getRestaurantTable(): ?Table
    {
        return $this->restaurantTable;
    }

    public function setRestaurantTable(?Table $restaurantTable): static
    {
        $this->restaurantTable = $restaurantTable;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setReservation($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getReservation() === $this) {
                $payment->setReservation(null);
            }
        }

        return $this;
    }
}
