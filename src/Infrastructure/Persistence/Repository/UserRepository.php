<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use App\Domain\Repository\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    private EntityManagerInterface $em;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository(User::class);
    }

    public function findById(int $id): ?User
    {
        return $this->repository->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    public function findByPhoneNumber(string $phoneNumber): ?User
    {
        return $this->repository->findOneBy(['phoneNumber' => $phoneNumber]);
    }

    public function searchByName(string $name): array
    {
        return $this->em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.firstname LIKE :name')
            ->orWhere('u.lastname LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function emailExists(string $email): bool
    {
        $count = $this->em->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(User::class, 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function delete(User $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}