<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Model\Review;
use App\Domain\Repository\ReviewRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class ReviewRepository implements ReviewRepositoryInterface
{
    private EntityManagerInterface $em;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(Review::class);
    }

    public function findById(int $id): ?Review
    {
        return $this->repository->find($id);
    }

    public function findByUser(int $userId): array
    {
        return $this->repository->findBy(['user' => $userId], ['createdAt' => 'DESC']);
    }

    public function findByRestaurant(int $restaurantId): array
    {
        return $this->repository->findBy(['restaurant' => $restaurantId], ['createdAt' => 'DESC']);
    }

    public function findByRating(int $rating): array
    {
        return $this->repository->findBy(['ratin' => $rating], ['createdAt' => 'DESC']);
    }

    public function findRecentReviews(int $restaurantId, int $limit = 10): array
    {
        return $this->em->createQueryBuilder()
            ->select('r')
            ->from(Review::class, 'r')
            ->where('r.restaurant = :restaurantId')
            ->setParameter('restaurantId', $restaurantId)
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getAverageRating(int $restaurantId): ?float
    {
        return $this->em->createQueryBuilder()
            ->select('AVG(r.rating)')
            ->from(Review::class, 'r')
            ->where('r.restaurant = :restaurantId')
            ->setParameter('restaurantId', $restaurantId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function save(Review $review): void
    {
        $this->em->persist($review);
        $this->em->flush();
    }

    public function delete(Review $review): void
    {
        $this->em->remove($review);
        $this->em->flush();
    }
}