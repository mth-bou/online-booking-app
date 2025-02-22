<?php

namespace App\Infrastructure\Controller;

use App\Application\Port\ReservationUseCaseInterface;
use App\Application\DTO\Reservation\ReservationRequestDTO;
use App\Application\DTO\Reservation\ReservationResponseDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Reservations")]
class ReservationController extends AbstractController
{
    private ReservationUseCaseInterface $reservationService;
    private ValidatorInterface $validator;

    public function __construct(ReservationUseCaseInterface $reservationService, ValidatorInterface $validator)
    {
        $this->reservationService = $reservationService;
        $this->validator = $validator;
    }

    #[Route('/reservations', methods: ['POST'])]
    #[OA\Post(
        path: "/reservations",
        summary: "Create a new reservation",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: ReservationRequestDTO::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Reservation successfully created",
                content: new OA\JsonContent(ref: new Model(type: ReservationResponseDTO::class))
            ),
            new OA\Response(response: 400, description: "Invalid input data")
        ]
    )]
    public function createReservation(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new ReservationRequestDTO(
            $data['userId'] ?? 0,
            $data['tableId'] ?? 0,
            $data['timeSlotId'] ?? 0,
            $data['status'] ?? null
        );

        $errors = $this->validator->validate($dto);

        if ($errors->count() > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $reservation = $this->reservationService->createReservation(
            $dto->userId,
            $dto->tableId,
            $dto->timeSlotId,
            $dto->status,
        );

        return new JsonResponse(new ReservationResponseDTO($reservation), Response::HTTP_CREATED);
    }

    #[Route('/reservations/{id}/cancel', methods: ['PATCH'])]
    #[OA\Patch(
        path: "/reservations/{id}/cancel",
        summary: "Cancel a reservation",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Reservation canceled"),
            new OA\Response(response: 404, description: "Reservation not found")
        ]
    )]
    public function cancelReservation(int $id): JsonResponse
    {
        $this->reservationService->cancelReservation($id);
        return new JsonResponse(['message' => 'Reservation canceled.'], Response::HTTP_OK);
    }

    #[Route('/reservations/{id}/confirm', methods: ['PATCH'])]
    #[OA\Patch(
        path: "/reservations/{id}/confirm",
        summary: "Confirm a reservation",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Reservation confirmed"),
            new OA\Response(response: 404, description: "Reservation not found")
        ]
    )]
    public function confirmReservation(int $id): JsonResponse
    {
        $this->reservationService->confirmReservation($id);
        return new JsonResponse(['message' => 'Reservation confirmed.'], Response::HTTP_OK);
    }

    #[Route('/reservations/user/{userId}', methods: ['GET'])]
    #[OA\Get(
        path: "/reservations/user/{userId}",
        summary: "Get reservations for a user",
        parameters: [
            new OA\Parameter(name: "userId", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of user reservations",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: ReservationResponseDTO::class)))
            ),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function getUserReservations(int $userId): JsonResponse
    {
        $reservations = $this->reservationService->getUserReservations($userId);
        return new JsonResponse(array_map(static fn($r) => new ReservationResponseDTO($r), $reservations), Response::HTTP_OK);
    }

    #[Route('/reservations/restaurant/{restaurantId}', methods: ['GET'])]
    #[OA\Get(
        path: "/reservations/restaurant/{restaurantId}",
        summary: "Get reservations for a restaurant",
        parameters: [
            new OA\Parameter(name: "restaurantId", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of restaurant reservations",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: ReservationResponseDTO::class)))
            ),
            new OA\Response(response: 404, description: "Restaurant not found")
        ]
    )]
    public function getRestaurantReservations(int $restaurantId): JsonResponse
    {
        $reservations = $this->reservationService->getRestaurantReservations($restaurantId);
        return new JsonResponse(array_map(static fn($r) => new ReservationResponseDTO($r), $reservations), Response::HTTP_OK);
    }
}