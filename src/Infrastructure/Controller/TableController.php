<?php

namespace App\Infrastructure\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Port\TableUseCaseInterface;
use App\Application\DTO\Table\TableRequestDTO;
use App\Application\DTO\Table\TableResponseDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use DateTime;

class TableController extends AbstractController
{
    private TableUseCaseInterface $tableService;
    private ValidatorInterface $validator;

    public function __construct(
        TableUseCaseInterface $tableService,
        ValidatorInterface $validator
    ) {
        $this->tableService = $tableService;
        $this->validator = $validator;
    }

    #[Route('/tables', methods: ['POST'])]
    public function addTable(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new TableRequestDTO(
            $data['restaurantId'] ?? 0,
            $data['capacity'] ?? 0,
            $data['tableNumber'] ?? ''
        );

        $errors = $this->validator->validate($dto);
        if ($errors->count() > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $table = $this->tableService->addTable(
                $dto->restaurantId,
                $dto->capacity,
                $dto->tableNumber
            );

            return new JsonResponse(new TableResponseDTO($table), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/tables/{id}/availability', methods: ['GET'])]
    public function isTableAvailable(int $id, Request $request): JsonResponse
    {
        $startTime = new DateTime($request->query->get('startTime'));
        $endTime = new DateTime($request->query->get('endTime'));

        try {
            $isAvailable = $this->tableService->isTableAvailable($id, $startTime, $endTime);
            return new JsonResponse(['available' => $isAvailable], Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}