<?php

namespace App\Infrastructure\Controller;

use DateTimeImmutable;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Port\TimeSlotUseCaseInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\DTO\TimeSlot\TimeSlotRequestDTO;
use App\Application\DTO\TimeSlot\TimeSlotResponseDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

#[OA\Tag(name: "TimeSlots")]
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
    #[OA\Post(
        path: "/timeslots",
        summary: "Add a new time slot",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: TimeSlotRequestDTO::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Time slot created",
                content: new OA\JsonContent(ref: new Model(type: TimeSlotResponseDTO::class))
            ),
            new OA\Response(response: 400, description: "Invalid input data")
        ]
    )]
    public function addTimeSlot(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

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
    #[OA\Get(
        path: "/timeslots/{id}",
        summary: "Find a time slot by ID",
        responses: [
            new OA\Response(
                response: 200,
                description: "Time slot found",
                content: new OA\JsonContent(ref: new Model(type: TimeSlotResponseDTO::class))
            ),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: "Time slot not found")
        ]
    )]
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

    #[Route('/restaurants/{restaurantId}/available-timeslots', methods: ['GET'])]
    #[OA\Get(
        path: "/restaurants/{restaurantId}/timeslots",
        summary: "Get available time slots for a restaurant",
        parameters: [
            new OA\Parameter(name: "restaurantId", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of available time slots",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: TimeSlotResponseDTO::class)))
            ),
            new OA\Response(response: 404, description: "Restaurant not found")
        ]
    )]
    public function getAvailableTimeSlots(int $restaurantId): JsonResponse
    {
        try {
            $timeSlots = $this->timeSlotService->getAvailableTimeSlots($restaurantId);
            return new JsonResponse(array_map(static fn($t) => new TimeSlotResponseDTO($t), $timeSlots), Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/timeslots/{id}', methods: ['PATCH'])]
    #[OA\Patch(
        path: "/timeslots/{id}",
        summary: "Update a time slot",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "startTime", type: "string", format: "date-time", example: "2025-02-01 19:00:00"),
                    new OA\Property(property: "endTime", type: "string", format: "date-time", example: "2025-02-01 21:00:00"),
                    new OA\Property(property: "isAvailable", type: "boolean", example: true)
                ]
            )
        ),
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Time slot updated",
                content: new OA\JsonContent(ref: new Model(type: TimeSlotResponseDTO::class))
            ),
            new OA\Response(response: Response::HTTP_NO_CONTENT, description: "Invalid input data"),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: "Invalid date format")
        ]
    )]
    public function updateTimeSlot(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

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
    #[OA\Delete(
        path: "/timeslots/{id}",
        summary: "Delete a time slot",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: Response::HTTP_NO_CONTENT, description: "Time slot deleted successfully"),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: "Time slot not found")
        ]
    )]
    public function deleteTimeSlot(int $id): JsonResponse
    {
        try {
            $this->timeSlotService->deleteTimeSlot($id);
            return new JsonResponse(['message' => 'Time slot deleted successfully.'], Response::HTTP_NO_CONTENT);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}