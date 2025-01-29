<?php

namespace App\Application\Service;

use App\Domain\Enum\StatusEnum;
use App\Domain\Model\Payment;
use App\Domain\Repository\PaymentRepositoryInterface;
use App\Domain\Repository\ReservationRepositoryInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use DateTimeImmutable;

class PaymentService
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

        $payment = $this->paymentRepository->createNew();
        $payment->setReservation($reservation);
        $payment->setAmount($amount);
        $payment->setPaymentMethod($paymentMethod);
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
        $this->paymentRepository->save($payment);
    }

    public function refundPayment(int $paymentId): void
    {
        $payment = $this->paymentRepository->findById($paymentId);
        if (!$payment) {
            throw new NotFoundResourceException("Payment not found.");
        }

        $payment->setStatus(StatusEnum::REFUNDED->value);
        $this->paymentRepository->save($payment);
    }
}