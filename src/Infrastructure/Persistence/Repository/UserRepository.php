<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Model\User;
use App\Domain\Model\Interface\UserInterface;
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
        $this->repository = $em->getRepository(UserInterface::class);
    }

    public function createNew(): UserInterface
    {
        return new User();
    }

    public function findById(int $id): ?UserInterface
    {
        return $this->repository->find($id);
    }

    public function findByEmail(string $email): ?UserInterface
    {
        return $this->repository->findOneBy(['email' => $email]);
    }

    public function findByPhoneNumber(string $phoneNumber): ?UserInterface
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

    public function save(UserInterface $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function delete(UserInterface $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }
}