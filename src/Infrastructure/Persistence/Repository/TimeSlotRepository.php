<?php

namespace App\Infrastructure\Persistence\Repository;

use DateTime;
use App\Domain\Model\TimeSlot;
use App\Domain\Model\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use App\Domain\Model\Interface\TimeSlotInterface;
use App\Domain\Repository\TimeSlotRepositoryInterface;

class TimeSlotRepository implements TimeSlotRepositoryInterface
{
    private EntityManagerInterface $em;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(TimeSlot::class);
    }

    public function createNew(): TimeSlotInterface
    {
        return new TimeSlot();
    }

    public function findById(int $id): ?TimeSlotInterface
    {
        return $this->repository->find($id);
    }

    public function findAvailableByRestaurant(int $restaurantId): array
    {
        return $this->em->createQueryBuilder()
            ->select('ts')
            ->from(TimeSlot::class, 'ts')
            ->join('ts.table', 't')
            ->where('t.restaurant = :restaurantId')
            ->setParameter('restaurantId', $restaurantId)
            ->getQuery()
            ->getResult();

    }

    public function findByTable(int $tableId): array
    {
        return $this->repository->findBy(['table' => $tableId]);
    }

    public function findByDate(int $restaurantId, DateTime $date): array
    {
        return $this->em->createQueryBuilder()
            ->select('ts')
            ->from(TimeSlot::class, 'ts')
            ->join('ts.table', 't')
            ->where('t.restaurant = :restaurantId')
            ->andWhere('DATE(t.startTime) = :date')
            ->setParameter('restaurantId', $restaurantId)
            ->setParameter('date', $date->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function isTimeSlotAvailable(int $tableId, DateTime $startTime, DateTime $endTime): bool
    {
        $count = $this->em->createQueryBuilder()
            ->select('COUNT(r.id')
            ->from(Reservation::class, 'r')
            ->where('r.table = :tableId')
            ->andWhere('r.timeSlot.startTime < :endTime')
            ->andWhere('r.timeSlot.endTime > :startTime')
            ->setParameter('tableId', $tableId)
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->getQuery()
            ->getSingleScalarResult();

        return $count == 0;
    }

    public function save(TimeSlotInterface $timeSlot): void
    {
        $this->em->persist($timeSlot);
        $this->em->flush();
    }

    public function delete(TimeSlotInterface $timeSlot): void
    {
        $this->em->remove($timeSlot);
        $this->em->flush();
    }
}