<?php

namespace App\Infrastructure\Controller;

use App\Application\Port\UserUseCaseInterface;
use App\Application\DTO\User\UserRequestDTO;
use App\Application\DTO\User\UserResponseDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private UserUseCaseInterface $userService;
    private ValidatorInterface $validator;

    public function __construct(
        UserUseCaseInterface $userService,
        ValidatorInterface $validator
    ) {
        $this->userService = $userService;
        $this->validator = $validator;
    }

    #[Route('/users', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = new UserRequestDTO(
            $data['email'] ?? '',
            $data['password'] ?? '',
            $data['firstname'] ?? null,
            $data['lastname'] ?? null,
            $data['phoneNumber'] ?? null
        );

        $errors = $this->validator->validate($dto);
        if ($errors->count() > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userService->createUser(
            $dto->email,
            $dto->password,
            $dto->firstname,
            $dto->lastname,
            $dto->phoneNumber
        );

        return new JsonResponse(new UserResponseDTO($user), Response::HTTP_CREATED);
    }

    #[Route('/users/{id}', methods: ['GET'])]
    public function getUserById(int $id): JsonResponse
    {
        $user = $this->userService->findUserById($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(new UserResponseDTO($user), Response::HTTP_OK);
    }

    #[Route('/users/{id}', methods: ['PATCH'])]
    public function updateUser(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->userService->updateUser($id, $data);

        return new JsonResponse(new UserResponseDTO($user), Response::HTTP_OK);
    }

    #[Route('/users/{id}', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        $this->userService->deleteUser($id);
        return new JsonResponse(['message' => 'User deleted.'], Response::HTTP_NO_CONTENT);
    }

    #[Route('/users/{id}/reservations', methods: ['GET'])]
    public function getUserReservations(int $id): JsonResponse
    {
        $reservations = $this->userService->getUserReservations($id);
        return new JsonResponse($reservations, Response::HTTP_OK);
    }

    #[Route('/users/{id}/reviews', methods: ['GET'])]
    public function getUserReviews(int $id): JsonResponse
    {
        $reviews = $this->userService->getUserReviews($id);
        return new JsonResponse($reviews, Response::HTTP_OK);
    }

    #[Route('/users/{id}/notifications', methods: ['GET'])]
    public function getUserNotifications(int $id): JsonResponse
    {
        $notifications = $this->userService->getUserNotifications($id);
        return new JsonResponse($notifications, Response::HTTP_OK);
    }

    #[Route('/users/{id}/notifications/unread', methods: ['GET'])]
    public function getUnreadNotifications(int $id): JsonResponse
    {
        $notifications = $this->userService->getUnreadNotifications($id);
        return new JsonResponse($notifications, Response::HTTP_OK);
    }

    #[Route('/users/{id}/notifications/read-all', methods: ['PATCH'])]
    public function markAllNotificationsAsRead(int $id): JsonResponse
    {
        $this->userService->markAllNotificationsAsRead($id);
        return new JsonResponse(['message' => 'All notifications marked as read.'], Response::HTTP_OK);
    }
}