<?php

namespace App\Domain\Model;

use App\Infrastructure\Persistence\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    public const STATUS_PENDING = 'Pending';
    public const STATUS_CONFIRMED = 'Confirmed';
    public const STATUS_CANCELLED = 'Cancelled';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_REJECTED = 'Rejected';

    public const STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_CANCELLED,
        self::STATUS_COMPLETED,
        self::STATUS_REJECTED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TimeSlot $timeSlot = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Table $_table = null;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'reservation')]
    private Collection $payments;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        if (!in_array($status, self::STATUS_LIST, true)) {
            throw new \InvalidArgumentException("Invalid status: " . $status);
        }
        
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
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

    public function getTable(): ?Table
    {
        return $this->_table;
    }

    public function setTable(?Table $_table): static
    {
        $this->_table = $_table;

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
