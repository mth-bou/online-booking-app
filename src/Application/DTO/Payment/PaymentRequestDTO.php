<?php

namespace App\Application\DTO\Payment;

use App\Domain\Enum\PaymentMethodEnum;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $reservationId;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public float $amount;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [PaymentMethodEnum::class, 'casesAsArray'], message: "Invalid payment method.")]
    public string $paymentMethod;

    public function __construct(int $reservationId, float $amount, string $paymentMethod)
    {
        if (!PaymentMethodEnum::isValid($paymentMethod)) {
            throw new \InvalidArgumentException("Invalid payment method: " . $paymentMethod);
        }

        $this->reservationId = $reservationId;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
    }
}