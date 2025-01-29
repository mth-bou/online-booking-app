<?php

namespace App\Infrastructure\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Port\ReviewUseCaseInterface;
use App\Application\DTO\Review\ReviewRequestDTO;
use App\Application\DTO\Review\ReviewResponseDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class ReviewController extends AbstractController
{
    private ReviewUseCaseInterface $reviewService;
    private ValidatorInterface $validator;

    public function __construct(
        ReviewUseCaseInterface $reviewService,
        ValidatorInterface $validator
    ) {
        $this->reviewService = $reviewService;
        $this->validator = $validator;
    }

    #[Route('/reviews', methods: ['POST'])]
    public function addReview(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new ReviewRequestDTO(
            $data['userId'] ?? 0,
            $data['restaurantId'] ?? 0,
            $data['rating'] ?? 0,
            $data['comment'] ?? null
        );

        $errors = $this->validator->validate($dto);
        if ($errors->count() > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $review = $this->reviewService->addReview(
                $dto->userId,
                $dto->restaurantId,
                $dto->rating,
                $dto->comment
            );

            return new JsonResponse(new ReviewResponseDTO($review), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/restaurants/{id}/reviews', methods: ['GET'])]
    public function getRestaurantReviews(int $id): JsonResponse
    {
        try {
            $reviews = $this->reviewService->getRestaurantReviews($id);
            return new JsonResponse(array_map(fn($r) => new ReviewResponseDTO($r), $reviews), Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/users/{id}/reviews', methods: ['GET'])]
    public function getUserReviews(int $id): JsonResponse
    {
        try {
            $reviews = $this->reviewService->getUserReviews($id);
            return new JsonResponse(array_map(fn($r) => new ReviewResponseDTO($r), $reviews), Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/restaurants/{id}/rating', methods: ['GET'])]
    public function getAverageRating(int $id): JsonResponse
    {
        $rating = $this->reviewService->getAverageRating($id);
        return new JsonResponse(['averageRating' => $rating ?? 0.0], Response::HTTP_OK);
    }
}