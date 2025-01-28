<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Model\Restaurant;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use App\Domain\Model\Interface\RestaurantInterface;
use App\Domain\Repository\RestaurantRepositoryInterface;

class RestaurantRepository implements RestaurantRepositoryInterface
{
    private EntityManagerInterface $em;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Restaurant::class);
    }

    public function createNew(): RestaurantInterface
    {
        return new Restaurant();
    }

    public function findById(int $id): ?RestaurantInterface
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function findByName(string $name): ?RestaurantInterface
    {
        return $this->repository->findOneBy(['name' => $name]);
    }

    public function findByCity(string $city): array
    {
        return $this->repository->findBy(['city'=> $city]);
    }

    public function findByMinimumCapacity(int $capacity): array
    {
        return $this->em->createQueryBuilder()
            ->select('r')
            ->from(Restaurant::class, 'r')
            ->join('r.tables', 't')
            ->groupBy('r.id')
            ->having('SUM(t.capacity) >= :capacity')
            ->setParameter('capacity', $capacity)
            ->getQuery()
            ->getResult();
    }

    public function search(string $keyword): array
    {
        return $this->em->createQueryBuilder()
            ->select('r')
            ->from(Restaurant::class, 'r')
            ->where('r.name LIKE :keyword')
            ->orWhere('r.description LIKE :keyword')
            ->orWhere('r.city LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->getQuery()
            ->getResult();
    }

    public function save(RestaurantInterface $restaurant): void
    {
        $this->em->persist($restaurant);
        $this->em->flush();
    }

    public function delete(RestaurantInterface $restaurant): void
    {
        $this->em->remove($restaurant);
        $this->em->flush();
    }
}