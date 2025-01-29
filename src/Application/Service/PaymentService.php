<?php

namespace App\Application\Service;

use App\Application\Port\PaymentUseCaseInterface;
use App\Domain\Enum\StatusEnum;
use App\Domain\Model\Payment;
use App\Domain\Repository\PaymentRepositoryInterface;
use App\Domain\Repository\ReservationRepositoryInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use DateTimeImmutable;
use App\Domain\Enum\PaymentMethodEnum;

class PaymentService implements PaymentUseCaseInterface
{
    private PaymentRepositoryInterface $paymentRepository;
    private ReservationRepositoryInterface $reservationRepository;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        ReservationRepositoryInterface $reservationRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->reservationRepository = $reservationRepository;
    }

    public function processPayment(int $reservationId, float $amount, string $paymentMethod): Payment
    {
        $reservation = $this->reservationRepository->findById($reservationId);
        if (!$reservation) {
            throw new NotFoundResourceException("Reservation not found.");
        }

        if (!PaymentMethodEnum::isValid($paymentMethod)) {
            throw new \InvalidArgumentException("Invalid payment method: " . $paymentMethod);
        }

        $payment = $this->paymentRepository->createNew();
        $payment->setReservation($reservation);
        $payment->setAmount($amount);
        $payment->setPaymentMethod(PaymentMethodEnum::from($paymentMethod));
        $payment->setStatus(StatusEnum::PENDING->value);
        $payment->setPaymentDate(new DateTimeImmutable());

        $this->paymentRepository->save($payment);

        return $payment; 
    }

    public function confirmPayment(int $paymentId): void
    {
        $payment = $this->paymentRepository->findById($paymentId);
        if (!$payment) {
            throw new NotFoundResourceException("Payment not found.");
        }

        $payment->setStatus(StatusEnum::COMPLETED->value);
        $payment->setUpdatedAt(new DateTimeImmutable());
        $this->paymentRepository->save($payment);
    }

    public function refundPayment(int $paymentId): void
    {
        $payment = $this->paymentRepository->findById($paymentId);
        if (!$payment) {
            throw new NotFoundResourceException("Payment not found.");
        }

        $payment->setStatus(StatusEnum::REFUNDED->value);
        $payment->setUpdatedAt(new DateTimeImmutable());
        $this->paymentRepository->save($payment);
    }
}