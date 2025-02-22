<?php

namespace App\Infrastructure\Controller;

use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Port\RestaurantUseCaseInterface;
use App\Application\DTO\Restaurant\RestaurantRequestDTO;
use App\Application\DTO\Restaurant\RestaurantResponseDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Restaurants")]
class RestaurantController extends AbstractController
{
    private RestaurantUseCaseInterface $restaurantService;
    private ValidatorInterface $validator;

    public function __construct(
        RestaurantUseCaseInterface $restaurantService,
        ValidatorInterface $validator
    ) {
        $this->restaurantService = $restaurantService;
        $this->validator = $validator;
    }

    #[Route('/restaurants', methods: ['POST'])]
    #[OA\Post(
        path: "/restaurants",
        summary: "Create a new restaurant",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: RestaurantRequestDTO::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Restaurant created",
                content: new OA\JsonContent(ref: new Model(type: RestaurantResponseDTO::class))
            ),
            new OA\Response(response: 400, description: "Invalid input data")
        ]
    )]
    public function createRestaurant(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $dto = new RestaurantRequestDTO(
            $data['name'] ?? '',
            $data['type'] ?? '',
            $data['description'] ?? '',
            $data['address'] ?? '',
            $data['city'] ?? '',
            $data['postalCode'] ?? '',
            $data['phoneNumber'] ?? ''
        );

        $errors = $this->validator->validate($dto);
        if ($errors->count() > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $restaurant = $this->restaurantService->createRestaurant(
                $dto->name, 
                $dto->type, 
                $dto->description, 
                $dto->address, 
                $dto->city, 
                $dto->postalCode, 
                $dto->phoneNumber
            );

            return new JsonResponse(new RestaurantResponseDTO($restaurant), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/restaurants/{id}', methods: ['PATCH'])]
    #[OA\Patch(
        path: "/restaurants/{id}",
        summary: "Update a restaurant",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent()
        ),
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Restaurant updated", content: new OA\JsonContent(ref: new Model(type: RestaurantResponseDTO::class))),
            new OA\Response(response: 404, description: "Restaurant not found"),
            new OA\Response(response: 400, description: "Invalid data")
        ]
    )]
    public function updateRestaurant(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        try {
            $restaurant = $this->restaurantService->updateRestaurant($id, $data);
            return new JsonResponse(new RestaurantResponseDTO($restaurant), Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/restaurants/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        path: "/restaurants/{id}",
        summary: "Delete a restaurant",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Restaurant deleted"),
            new OA\Response(response: 404, description: "Restaurant not found")
        ]
    )]
    public function deleteRestaurant(int $id): JsonResponse
    {
        try {
            $this->restaurantService->deleteRestaurant($id);
            return new JsonResponse(['message' => 'Restaurant deleted.'], Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/restaurants/{id}', methods: ['GET'])]
    #[OA\Get(
        path: "/restaurants/{id}",
        summary: "Get a restaurant by ID",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Restaurant found", content: new OA\JsonContent(ref: new Model(type: RestaurantResponseDTO::class))),
            new OA\Response(response: 404, description: "Restaurant not found")
        ]
    )]
    public function getRestaurantById(int $id): JsonResponse
    {
        $restaurant = $this->restaurantService->getRestaurantById($id);
        if (!$restaurant) {
            return new JsonResponse(['error' => 'Restaurant not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(new RestaurantResponseDTO($restaurant), Response::HTTP_OK);
    }

    #[Route('/restaurants', methods: ['GET'])]
    #[OA\Get(
        path: "/restaurants",
        summary: "Get all restaurants",
        responses: [
            new OA\Response(
                response: 200,
                description: "List of restaurants",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: RestaurantResponseDTO::class))),
            )
        ]
    )]
    public function getAllRestaurants(): JsonResponse
    {
        $restaurants = $this->restaurantService->getAllRestaurants();
        return new JsonResponse(array_map(static fn($r) => new RestaurantResponseDTO($r), $restaurants), Response::HTTP_OK);
    }

    #[Route('/restaurants/search', methods: ['GET'])]
    #[OA\Get(
        path: "/restaurants/search",
        summary: "Search restaurants by keyword",
        parameters: [
            new OA\Parameter(name: "keyword", in: "query", required: false, schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of matching restaurants",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: RestaurantResponseDTO::class))),
            )
        ]
    )]
    public function searchRestaurants(Request $request): JsonResponse
    {
        $keyword = $request->query->get('keyword', '');
        $restaurants = $this->restaurantService->searchRestaurants($keyword);
        return new JsonResponse(array_map(static fn($r) => new RestaurantResponseDTO($r), $restaurants), Response::HTTP_OK);
    }

    #[Route('/restaurants/{id}/tables', methods: ['GET'])]
    #[OA\Get(
        path: "/restaurants/{id}/tables",
        summary: "Get tables for a restaurant",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "List of tables")
        ]
    )]
    public function getRestaurantTables(int $id): JsonResponse
    {
        $tables = $this->restaurantService->getRestaurantTables($id);
        return new JsonResponse($tables, Response::HTTP_OK);
    }
}