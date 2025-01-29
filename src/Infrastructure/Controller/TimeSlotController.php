<?php

namespace App\Infrastructure\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Port\TimeSlotUseCaseInterface;
use App\Application\DTO\TimeSlot\TimeSlotRequestDTO;
use App\Application\DTO\TimeSlot\TimeSlotResponseDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use DateTimeImmutable;

class TimeSlotController extends AbstractController
{
    private TimeSlotUseCaseInterface $timeSlotService;
    private ValidatorInterface $validator;

    public function __construct(
        TimeSlotUseCaseInterface $timeSlotService,
        ValidatorInterface $validator
    ) {
        $this->timeSlotService = $timeSlotService;
        $this->validator = $validator;
    }

    #[Route('/timeslots', methods: ['POST'])]
    public function addTimeSlot(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $startTime = new DateTimeImmutable($data['startTime']);
            $endTime = new DateTimeImmutable($data['endTime']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format.'], Response::HTTP_BAD_REQUEST);
        }

        $dto = new TimeSlotRequestDTO(
            $data['restaurantId'] ?? 0,
            $startTime,
            $endTime,
            $data['isAvailable'] ?? true
        );

        $errors = $this->validator->validate($dto);
        if ($errors->count() > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $timeSlot = $this->timeSlotService->addTimeSlot(
                $dto->restaurantId,
                $dto->startTime,
                $dto->endTime,
                $dto->isAvailable
            );

            return new JsonResponse(new TimeSlotResponseDTO($timeSlot), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/timeslots/{id}', methods: ['GET'])]
    public function findTimeSlotById(int $id): JsonResponse
    {
        try {
            $timeSlot = $this->timeSlotService->findTimeSlotById($id);
            if (!$timeSlot) {
                return new JsonResponse(['error' => 'Time slot not found.'], Response::HTTP_NOT_FOUND);
            }
            return new JsonResponse(new TimeSlotResponseDTO($timeSlot), Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/restaurants/{id}/timeslots', methods: ['GET'])]
    public function getAvailableTimeSlots(int $id): JsonResponse
    {
        try {
            $timeSlots = $this->timeSlotService->getAvailableTimeSlots($id);
            return new JsonResponse(array_map(fn($t) => new TimeSlotResponseDTO($t), $timeSlots), Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/timeslots/{id}', methods: ['PATCH'])]
    public function updateTimeSlot(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $startTime = isset($data['startTime']) ? new DateTimeImmutable($data['startTime']) : null;
            $endTime = isset($data['endTime']) ? new DateTimeImmutable($data['endTime']) : null;
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $timeSlot = $this->timeSlotService->updateTimeSlot(
                $id,
                $startTime,
                $endTime,
                $data['isAvailable'] ?? null
            );

            return new JsonResponse(new TimeSlotResponseDTO($timeSlot), Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/timeslots/{id}', methods: ['DELETE'])]
    public function deleteTimeSlot(int $id): JsonResponse
    {
        try {
            $this->timeSlotService->deleteTimeSlot($id);
            return new JsonResponse(['message' => 'Time slot deleted successfully.'], Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}