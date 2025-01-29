<?php

namespace App\Infrastructure\Controller;

use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Port\NotificationUseCaseInterface;
use App\Application\DTO\Notification\NotificationRequestDTO;
use App\Application\DTO\Notification\NotificationResponseDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Notifications")]
class NotificationController extends AbstractController
{
    private NotificationUseCaseInterface $notificationService;
    private ValidatorInterface $validator;

    public function __construct(
        NotificationUseCaseInterface $notificationService,
        ValidatorInterface $validator
    ) {
        $this->notificationService = $notificationService;
        $this->validator = $validator;
    }

    #[Route('/notifications', methods: ['POST'])]
    #[OA\Post(
        path: "/notifications",
        summary: "Create and send a notification",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "userId", type: "integer", example: 1),
                    new OA\Property(property: "message", type: "string", example: "Hello World"),
                    new OA\Property(property: "type", type: "string", example: "info")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Notification created",
                content: new OA\JsonContent(ref: new Model(type: NotificationResponseDTO::class))
            ),
            new OA\Response(response: 400, description: "Invalid data")
        ]
    )]
    public function sendNotification(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new NotificationRequestDTO(
            $data['userId'] ?? 0,
            $data['message'] ?? '',
            $data['type'] ?? ''
        );

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $notification = $this->notificationService->sendNotification(
            $dto->userId,
            $dto->message,
            $dto->type
        );

        return new JsonResponse(new NotificationResponseDTO($notification), Response::HTTP_CREATED);
    }

    #[Route('/notifications/{id}/sent', methods: ['PATCH'])]
    #[OA\Patch(
        path: "/notifications/{id}/sent",
        summary: "Mark notification as sent",
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        responses: [
            new OA\Response(response: 200, description: "Notification marked as sent"),
            new OA\Response(response: 404, description: "Notification not found")
        ]
    )]
    public function markNotificationAsSent(int $id): JsonResponse
    {
        $this->notificationService->markNotificationAsSent($id);
        return new JsonResponse(['message' => 'Notification marked as sent.'], Response::HTTP_OK);
    }

    #[Route('/notifications/{id}/read', methods: ['PATCH'])]
    #[OA\Patch(
        path: "/notifications/{id}/read",
        summary: "Mark notification as read",
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        responses: [
            new OA\Response(response: 200, description: "Notification marked as read"),
            new OA\Response(response: 404, description: "Notification not found")
        ]
    )]
    public function markNotificationAsRead(int $id): JsonResponse
    {
        $this->notificationService->markNotificationAsRead($id);
        return new JsonResponse(['message' => 'Notification marked as read.'], Response::HTTP_OK);
    }

    #[Route('/notifications/{id}', methods: ['GET'])]
    #[OA\Get(
        path: "/notifications/{id}",
        summary: "Retrieve a notification by ID",
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        responses: [
            new OA\Response(
                response: 200,
                description: "Notification details",
                content: new OA\JsonContent(ref: "#/components/schemas/NotificationResponseDTO")
            ),
            new OA\Response(response: 404, description: "Notification not found")
        ]
    )]
    public function getNotification(int $id): JsonResponse
    {
        $notification = $this->notificationService->getNotificationById($id);

        if (!$notification) {
            return new JsonResponse(['error' => 'Notification not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(new NotificationResponseDTO($notification), Response::HTTP_OK);
    }
}