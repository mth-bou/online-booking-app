<?php

namespace App\Infrastructure\Controller;

use App\Application\Port\ReservationUseCaseInterface;
use App\Application\DTO\Reservation\ReservationRequestDTO;
use App\Application\DTO\Reservation\ReservationResponseDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function createReservation(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = new ReservationRequestDTO($data['userId'] ?? 0, $data['tableId'] ?? 0, $data['timeSlotId'] ?? 0);
        $errors = $this->validator->validate($dto);

        if ($errors->count() > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $reservation = $this->reservationService->createReservation($dto->userId, $dto->tableId, $dto->timeSlotId);
        return new JsonResponse(new ReservationResponseDTO($reservation), Response::HTTP_CREATED);
    }

    #[Route('/reservations/{id}/cancel', methods: ['PATCH'])]
    public function cancelReservation(int $id): JsonResponse
    {
        $this->reservationService->cancelReservation($id);
        return new JsonResponse(['message' => 'Reservation canceled.'], Response::HTTP_OK);
    }

    #[Route('/reservations/{id}/confirm', methods: ['PATCH'])]
    public function confirmReservation(int $id): JsonResponse
    {
        $this->reservationService->confirmReservation($id);
        return new JsonResponse(['message' => 'Reservation confirmed.'], Response::HTTP_OK);
    }
}