<?php

namespace App\Infrastructure\Controller;

use App\Application\Port\NotificationUseCaseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NotificationController extends AbstractController
{
    private NotificationUseCaseInterface $notificationService;

    public function __construct(NotificationUseCaseInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    #[Route('/notifications', methods: ['POST'])]
    public function sendNotification(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $notification = $this->notificationService->sendNotification($data['userId'], $data['message']);

        return new JsonResponse(['notificationId' => $notification->getId()], Response::HTTP_CREATED);
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
}