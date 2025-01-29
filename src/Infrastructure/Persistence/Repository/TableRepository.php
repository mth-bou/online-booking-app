<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Model\Table;
use App\Domain\Repository\TableRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use DateTime;

class TableRepository implements TableRepositoryInterface
{
    private EntityManagerInterface $em;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Table::class);
    }

    public function createnew(): Table
    {
        return new Table();
    }

    public function findById(int $id): ?Table
    {
        return $this->repository->find($id);
    }

    public function findByRestaurant(int $restaurantId): array
    {
        return $this->repository->findBy(['restaurant' => $restaurantId]);
    }

    public function findByMinimumCapacity(int $capacity): array
    {
        return $this->em->createQueryBuilder()
            ->select('t')
            ->from(Table::class, 't')
            ->where('t.capacity >= :capacity')
            ->setParameter('capacity', $capacity)
            ->getQuery()
            ->getResult();
    }

    public function findAvailableTables(int $restaurantId, DateTime $startTime, DateTime $endTime, int $guests): array
    {
        return $this->em->createQueryBuilder()
            ->select('t')
            ->from(Table::class, 't')
            ->where('t.restaurant = :restaurantId')
            ->andWhere('t.capacity >= :guests')
            ->andWhere('t.id NOT IN (
                SELECT r.restaurantTable FROM \App\Domain\Model\Reservation r
                WHERE r.restaurant = :restaurantId
                AND r.timeSlot.starTime < :endTime
                AND r.timeSlot.endTime > :startTime
            ')
            ->setParameter('restaurantId', $restaurantId)
            ->setParameter('starTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->setParameter('guests', $guests)
            ->getQuery()
            ->getResult();
    }

    public function findReservedTables(int $restaurantId, DateTime $startTime, DateTime $endTime): array
    {
        return $this->em->createQueryBuilder()
            ->select('t')
            ->from(Table::class, 't')
            ->where('t.restaurant = :restaurantId')
            ->andWhere('EXISTS (
                SELECT 1 FROM App\Domain\Model\Reservation r
                WHERE r.restaurantTable = t
                AND r.timeSlot.startTime < :endTime
                AND r.timeSlot.endTime > :startTime
            )')
            ->setParameter('restaurantId', $restaurantId)
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->getQuery()
            ->getResult();
    }

    public function isTableAvailable(int $tableId, DateTime $startTime, DateTime $endTime): bool
    {
        $count = $this->em->createQueryBuilder()
            ->select('COUNT(r.id)')
            ->from('App\Domain\Model\Reservation', 'r')
            ->where('r.restaurantTable = :tableId')
            ->andWhere('r.timeSlot.startTime < :endTime')
            ->andWhere('r.timeSlot.endTime > :startTime')
            ->setParameter('tableId', $tableId)
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->getQuery()
            ->getSingleScalarResult();

        return $count == 0;
    }

    public function save(Table $table): void
    {
        $this->em->persist($table);
        $this->em->flush();
    }

    public function delete(Table $table): void
    {
        $this->em->remove($table);
        $this->em->flush();
    }
}