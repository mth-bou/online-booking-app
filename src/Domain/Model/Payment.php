<?php

namespace App\Domain\Model;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use App\Domain\Enum\StatusEnum;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Model\Reservation;
use App\Domain\Enum\PaymentMethodEnum;
use Symfony\Component\Validator\Constraints as Assert;
use App\Infrastructure\Persistence\Repository\PaymentRepository;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'payment',
    indexes: [
        new ORM\Index(name: 'IDX_PAYMENT_RESERVATION', columns: ['reservation_id']),
        new ORM\Index(name: 'IDX_PAYMENT_STATUS', columns: ['status'])
    ],
    options: [
        "check" => "amount >= 0 AND status IN ('PENDING', 'COMPLETED', 'FAILED', 'CANCELED', 'REFUNDED', 'REJECTED')"
    ]
)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $paymentDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?float $amount = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(callback: [StatusEnum::class, 'casesAsArray'], message: "Invalid Status.")]
    private string $status = StatusEnum::PENDING->value;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(callback: [PaymentMethodEnum::class, 'casesAsArray'], message: "Invalid payment method.")]
    private ?PaymentMethodEnum $paymentMethod = null;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(name: 'reservation_id', referencedColumnName: 'id', nullable: false)]
    private ?Reservation $reservation = null;

    public function __construct()
    {
        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        // $this->setStatus(StatusEnum::PENDING->value);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTimeInterface $paymentDate): static
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

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

    public function getPaymentMethod(): ?PaymentMethodEnum
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(PaymentMethodEnum $paymentMethod): static
    {
        if (!PaymentMethodEnum::isValid($paymentMethod->value)) {
            throw new \InvalidArgumentException("Invalid payment method: " . $paymentMethod);
        }

        $this->paymentMethod = $paymentMethod;

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

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;

        return $this;
    }
}
