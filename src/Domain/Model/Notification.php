<?php

namespace App\Domain\Model;

use App\Domain\Enum\NotificationStatusEnum;
use App\Domain\Model\User;
use App\Infrastructure\Persistence\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: 'notification',
    indexes: [
        new ORM\Index(name: 'IDX_NOTIFICATION_USER', columns: ['user_id']),
        new ORM\Index(name: 'IDX_NOTIFICATION_STATUS', columns: ['status'])
    ],
    options: [
        "check" => "status IN ('PENDING', 'SENT', 'FAILED') AND created_at <= updated_at"
    ]
)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(callback: [NotificationStatusEnum::class, 'casesAsArray'], message: "Invalid Status.")]
    private ?string $status = NotificationStatusEnum::PENDING->value;

    #[ORM\Column]
    private bool $isRead = false;

    #[ORM\Column(name: "created_at")]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: "updated_at")]
    private DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $_user = null;

    public function __construct()
    {
        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        if (!NotificationStatusEnum::isValid($status)) {
            throw new \InvalidArgumentException("Invalid status: " . $status);
        }

        $this->status = $status;

        return $this;
    }

    public function getIsRead(): bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;

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

    public function getUser(): ?User
    {
        return $this->_user;
    }

    public function setUser(?User $_user): static
    {
        $this->_user = $_user;

        return $this;
    }
}
