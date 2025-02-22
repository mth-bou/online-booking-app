<?php

namespace App\Infrastructure\Controller;

use DateTime;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use App\Application\DTO\Table\TableRequestDTO;
use Symfony\Component\HttpFoundation\Response;
use App\Application\DTO\Table\TableResponseDTO;
use App\Application\Port\TableUseCaseInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

#[OA\Tag(name: "Tables")]
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
    #[OA\Post(
        path: "/tables",
        summary: "Add a new table to a restaurant",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: TableRequestDTO::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Table created",
                content: new OA\JsonContent(ref: new Model(type: TableResponseDTO::class))
            ),
            new OA\Response(response: 400, description: "Invalid input data")
        ]
    )]
    public function addTable(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

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
    #[OA\Get(
        path: "/tables/{id}/availability",
        summary: "Check if a table is available for a given time range",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "startTime", in: "query", required: true, schema: new OA\Schema(type: "string", format: "date-time"), example: "2025-02-01 19:00:00"),
            new OA\Parameter(name: "endTime", in: "query", required: true, schema: new OA\Schema(type: "string", format: "date-time"), example: "2025-02-01 21:00:00")
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Availability status",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "available", type: "boolean", example: true)
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Table not found")
        ]
    )]
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