<?php

namespace App\Infrastructure\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Port\RestaurantUseCaseInterface;
use App\Application\DTO\Restaurant\RestaurantRequestDTO;
use App\Application\DTO\Restaurant\RestaurantResponseDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

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
    public function createRestaurant(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

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
    public function updateRestaurant(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

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
    public function getRestaurantById(int $id): JsonResponse
    {
        $restaurant = $this->restaurantService->getRestaurantById($id);
        if (!$restaurant) {
            return new JsonResponse(['error' => 'Restaurant not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(new RestaurantResponseDTO($restaurant), Response::HTTP_OK);
    }

    #[Route('/restaurants', methods: ['GET'])]
    public function getAllRestaurants(): JsonResponse
    {
        $restaurants = $this->restaurantService->getAllRestaurants();
        return new JsonResponse(array_map(fn($r) => new RestaurantResponseDTO($r), $restaurants), Response::HTTP_OK);
    }

    #[Route('/restaurants/search', methods: ['GET'])]
    public function searchRestaurants(Request $request): JsonResponse
    {
        $keyword = $request->query->get('keyword', '');
        $restaurants = $this->restaurantService->searchRestaurants($keyword);
        return new JsonResponse(array_map(fn($r) => new RestaurantResponseDTO($r), $restaurants), Response::HTTP_OK);
    }

    #[Route('/restaurants/{id}/tables', methods: ['GET'])]
    public function getRestaurantTables(int $id): JsonResponse
    {
        $tables = $this->restaurantService->getRestaurantTables($id);
        return new JsonResponse($tables, Response::HTTP_OK);
    }

    #[Route('/restaurants/{id}/reviews', methods: ['GET'])]
    public function getRestaurantReviews(int $id): JsonResponse
    {
        $reviews = $this->restaurantService->getRestaurantReviews($id);
        return new JsonResponse($reviews, Response::HTTP_OK);
    }

    #[Route('/restaurants/{id}/rating', methods: ['GET'])]
    public function getAverageRating(int $id): JsonResponse
    {
        $rating = $this->restaurantService->calculateAverageRating($id);
        return new JsonResponse(['averageRating' => $rating], Response::HTTP_OK);
    }
}