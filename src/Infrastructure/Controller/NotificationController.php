<?php

namespace App\Infrastructure\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Port\NotificationUseCaseInterface;
use App\Application\DTO\Notification\NotificationRequestDTO;
use App\Application\DTO\Notification\NotificationResponseDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function sendNotification(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new NotificationRequestDTO($data['userId'] ?? 0, $data['message'] ?? '');
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $notification = $this->notificationService->sendNotification($dto->userId, $dto->message);

        return new JsonResponse(new NotificationResponseDTO($notification), Response::HTTP_CREATED);
    }

    #[Route('/notifications/{id}/sent', methods: ['PATCH'])]
    public function markNotificationAsSent(int $id): JsonResponse
    {
        $this->notificationService->markNotificationAsSent($id);
        return new JsonResponse(['message' => 'Notification marked as sent.'], Response::HTTP_OK);
    }

    #[Route('/notifications/{id}/read', methods: ['PATCH'])]
    public function markNotificationAsRead(int $id): JsonResponse
    {
        $this->notificationService->markNotificationAsRead($id);
        return new JsonResponse(['message' => 'Notification marked as read.'], Response::HTTP_OK);
    }

    #[Route('/notifications/{id}', methods: ['GET'])]
    public function getNotification(int $id): JsonResponse
    {
        $notification = $this->notificationService->getNotificationById($id);

        if (!$notification) {
            return new JsonResponse(['error' => 'Notification not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(new NotificationResponseDTO($notification), Response::HTTP_OK);
    }
}