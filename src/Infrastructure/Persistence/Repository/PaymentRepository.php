<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Model\Payment;
use App\Domain\Repository\PaymentRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use DateTime;

class PaymentRepository implements PaymentRepositoryInterface
{
    private EntityManagerInterface $em;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Payment::class);
    }

    public function findById(int $id): ?Payment
    {
        return $this->repository->find($id);
    }

    public function findByReservation(int $reservationId): array
    {
        return $this->repository->findBy(['reservation' => $reservationId]);
    }

    public function findByUser(int $userId): array
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from(Payment::class, 'p')
            ->join('p.reservation', 'r')
            ->where('r.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findByRestaurant(int $restaurantId): array
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from(Payment::class, 'p')
            ->join('p.reservation', 'r')
            ->join('r.table', 't')
            ->join('t.restaurant', 'rest')
            ->where('rest.id = :restaurantId')
            ->setParameter('restaurantId', $restaurantId)
            ->getQuery()
            ->getResult();
    }

    public function findByStatus(string $status): array
    {
        if (!in_array($status, Payment::STATUS_LIST, true)) {
            throw new \InvalidArgumentException("Invalid status: " . $status);
        }

        return $this->repository->findBy(['status' => $status]);
    }

    public function findPendingPayments(): array
    {
        return $this->findByStatus(Payment::STATUS_PENDING);
    }

    public function findCompletedPayments(): array
    {
        return $this->findByStatus(Payment::STATUS_COMPLETED);
    }

    public function findFailedPayments(): array
    {
        return $this->findByStatus(Payment::STATUS_FAILED);
    }

    public function findRefundedPayments(): array
    {
        return $this->findByStatus(Payment::STATUS_REFUNDED);
    }

    public function updatePaymentStatus(int $paymentId, string $status): void
    {
        $payment = $this->findById($paymentId);
        if (!$payment) {
            throw new \Exception("Payment not found.");
        }

        $payment->setStatus($status);
        $this->save($payment);
    }

    public function findByDateRange(DateTime $startDate, DateTime $endDate): array
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from(Payment::class, 'p')
            ->where('p.paymentDate BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

    public function save(Payment $payment): void
    {
        $this->em->persist($payment);
        $this->em->flush();
    }

    public function delete(Payment $payment): void
    {
        $this->em->remove($payment);
        $this->em->flush();
    }
}