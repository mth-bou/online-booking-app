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
    public PaymentMethodEnum $paymentMethod;

    public function __construct(int $reservationId, float $amount, PaymentMethodEnum $paymentMethod)
    {
        $this->reservationId = $reservationId;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
    }
}