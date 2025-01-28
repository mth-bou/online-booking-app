<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Model\Restaurant;
use App\Domain\Repository\RestaurantRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class RestaurantRepository implements RestaurantRepositoryInterface
{
    private EntityManagerInterface $em;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Restaurant::class);
    }

    public function findById(int $id): ?Restaurant
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function findByName(string $name): ?Restaurant
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

    public function save(Restaurant $restaurant): void
    {
        $this->em->persist($restaurant);
        $this->em->flush();
    }

    public function delete(Restaurant $restaurant): void
    {
        $this->em->remove($restaurant);
        $this->em->flush();
    }
}