<?php

namespace App\Infrastructure\Controller;

use App\Application\DTO\Reservation\ReservationResponseDTO;
use App\Application\DTO\Review\ReviewResponseDTO;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use App\Application\DTO\User\UserRequestDTO;
use App\Application\DTO\User\UserResponseDTO;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Port\UserUseCaseInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[OA\Tag(name: "Users")]
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
    #[OA\Post(
        path: "/users",
        summary: "Create a new user",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: UserRequestDTO::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User created",
                content: new OA\JsonContent(ref: new Model(type: UserResponseDTO::class))
            ),
            new OA\Response(response: 400, description: "Invalid input data")
        ]
    )]
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
    #[OA\Get(
        path: "/users/{id}",
        summary: "Get a user by ID",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "User found",
                content: new OA\JsonContent(ref: new Model(type: UserResponseDTO::class))
            ),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function getUserById(int $id): JsonResponse
    {
        $user = $this->userService->findUserById($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(new UserResponseDTO($user), Response::HTTP_OK);
    }

    #[Route('/users/{id}', methods: ['PATCH'])]
    #[OA\Patch(
        path: "/users/{id}",
        summary: "Update a user",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "john.update@example.com"),
                    new OA\Property(property: "password", type: "string", example: "newPassword", nullable: true),
                    new OA\Property(property: "firstname", type: "string", example: "Johnny", nullable: true),
                    new OA\Property(property: "lastname", type: "string", example: "Updated", nullable: true),
                    new OA\Property(property: "phoneNumber", type: "string", example: "+441234567890", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "User updated",
                content: new OA\JsonContent(ref: new Model(type: UserResponseDTO::class))
            ),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function updateUser(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->userService->updateUser($id, $data);

        return new JsonResponse(new UserResponseDTO($user), Response::HTTP_OK);
    }

    #[Route('/users/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/users/{id}",
        summary: "Delete a user",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 204, description: "User deleted"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function deleteUser(int $id): JsonResponse
    {
        $this->userService->deleteUser($id);
        return new JsonResponse(['message' => 'User deleted.'], Response::HTTP_NO_CONTENT);
    }

    #[Route('/users/{id}/reservations', methods: ['GET'])]
    #[OA\Get(
        path: "/users/{id}/reservations",
        summary: "Get all reservations for a user",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of reservations",
                content: new OA\JsonContent(type: "array", items: new OA\Items(type: ReservationResponseDTO::class))
            ),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function getUserReservations(int $id): JsonResponse
    {
        $reservations = $this->userService->getUserReservations($id);
        return new JsonResponse($reservations, Response::HTTP_OK);
    }

    #[Route('/users/{id}/reviews', methods: ['GET'])]
    #[OA\Get(
        path: "/users/{id}/reviews",
        summary: "Get all reviews by a user",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of reviews",
                content: new OA\JsonContent(type: "array", items: new OA\Items(type: ReviewResponseDTO::class))
            ),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function getUserReviews(int $id): JsonResponse
    {
        $reviews = $this->userService->getUserReviews($id);
        return new JsonResponse($reviews, Response::HTTP_OK);
    }

    #[Route('/users/{id}/notifications', methods: ['GET'])]
    #[OA\Get(
        path: "/users/{id}/notifications",
        summary: "Get all notifications for a user",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of notifications",
                content: new OA\JsonContent(type: "array", items: new OA\Items(type: "object"))
            ),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function getUserNotifications(int $id): JsonResponse
    {
        $notifications = $this->userService->getUserNotifications($id);
        return new JsonResponse($notifications, Response::HTTP_OK);
    }

    #[Route('/users/{id}/notifications/unread', methods: ['GET'])]
    #[OA\Get(
        path: "/users/{id}/notifications/unread",
        summary: "Get all unread notifications for a user",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of unread notifications",
                content: new OA\JsonContent(type: "array", items: new OA\Items(type: "object"))
            ),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function getUnreadNotifications(int $id): JsonResponse
    {
        $notifications = $this->userService->getUnreadNotifications($id);
        return new JsonResponse($notifications, Response::HTTP_OK);
    }

    #[Route('/users/{id}/notifications/read-all', methods: ['PATCH'])]
    #[OA\Patch(
        path: "/users/{id}/notifications/read-all",
        summary: "Mark all notifications as read for a user",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "All notifications marked as read"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function markAllNotificationsAsRead(int $id): JsonResponse
    {
        $this->userService->markAllNotificationsAsRead($id);
        return new JsonResponse(['message' => 'All notifications marked as read.'], Response::HTTP_OK);
    }
}