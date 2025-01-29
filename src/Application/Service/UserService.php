<?php

namespace App\Application\Service;

use Exception;
use DateTimeImmutable;
use App\Domain\Model\User;
use App\Application\Port\UserUseCaseInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Repository\ReviewRepositoryInterface;
use App\Domain\Repository\ReservationRepositoryInterface;
use App\Domain\Repository\NotificationRepositoryInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService implements UserUseCaseInterface
{
    private UserRepositoryInterface $userRepository;
    private ReservationRepositoryInterface $reservationRepository;
    private ReviewRepositoryInterface $reviewRepository;
    private NotificationRepositoryInterface $notificationRepository;
    private UserPasswordHasherInterface $passwordHasher;


    public function __construct(
        UserRepositoryInterface $userRepository,
        ReservationRepositoryInterface $reservationRepository,
        ReviewRepositoryInterface $reviewRepository,
        NotificationRepositoryInterface $notificationRepository,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->reservationRepository = $reservationRepository;
        $this->reviewRepository = $reviewRepository;
        $this->notificationRepository = $notificationRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function createUser(
        string $email,
        string $password,
        ?string $firstname,
        ?string $lastname,
        ?string $phoneNumber
        ): User
    {
        if ($this->userRepository->emailExists($email)) {
            throw new Exception("Email already in use.");
        }

        $user = $this->userRepository->createNew();
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setPhoneNumber($phoneNumber);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->userRepository->save($user);

        return $user;
    }

    public function authenticateUser(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return null;
        }

        return $user;
    }

    public function findUserById(int $userId): ?User
    {
        return $this->userRepository->findById($userId);
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function updateUser(int $userId, array $data): User
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) throw new NotFoundResourceException("User not found.");

        if (isset($data['firstname'])) $user->setFirstname($data['firstname']);

        if (isset($data['lastname'])) $user->setLastname($data['lastname']);

        if (isset($data['phoneNumber'])) $user->setPhoneNumber($data['phoneNumber']);

        if (isset($data['password'])) $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

        $user->setUpdatedAt(new DateTimeImmutable());

        $this->userRepository->save($user);

        return $user;
    }

    public function deleteUser(int $userId): void
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new NotFoundResourceException("User not found.");
        }

        $this->userRepository->delete($user);
    }

    public function getUserReservations(int $userId): array
    {
        return $this->reservationRepository->findByUser($userId);
    }

    public function getUserReviews(int $userId): array
    {
        return $this->reviewRepository->findByUser($userId);
    }

    public function getUserNotifications(int $userId): array
    {
        return $this->notificationRepository->findByUser($userId);
    }

    public function getUnreadNotifications(int $userId): array
    {
        return $this->notificationRepository->findUnreadByUser($userId);
    }

    public function markAllNotificationsAsRead(int $userId): void
    {
        $this->notificationRepository->markAllAsRead($userId);
    }
}