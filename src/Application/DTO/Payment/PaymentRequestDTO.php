<?php

namespace App\Application\DTO\Payment;

use App\Domain\Enum\PaymentMethodEnum;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema]
class PaymentRequestDTO
{
    #[OA\Property(type: "integer", example: 123)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $reservationId;

    #[OA\Property(type: "number", format: "float", example: 99.99)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public float $amount;

    #[OA\Property(type: "string", example: "credit_card")]
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