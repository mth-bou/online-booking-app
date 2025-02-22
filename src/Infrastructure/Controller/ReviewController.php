<?php

namespace App\Infrastructure\Controller;

use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Port\ReviewUseCaseInterface;
use App\Application\DTO\Review\ReviewRequestDTO;
use App\Application\DTO\Review\ReviewResponseDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Reviews")]
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
    #[OA\Post(
        path: "/reviews",
        summary: "Add a new review",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: ReviewRequestDTO::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Review created",
                content: new OA\JsonContent(ref: new Model(type: ReviewResponseDTO::class))
            ),
            new OA\Response(response: 400, description: "Invalid input data")
        ]
    )]
    public function addReview(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

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

    #[Route('/reviews/restaurant/{id}', methods: ['GET'])]
    #[OA\Get(
        path: "/reviews/restaurant/{id}",
        summary: "Get reviews for a restaurant",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of reviews",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: ReviewResponseDTO::class))),
            ),
            new OA\Response(response: 404, description: "Restaurant not found")
        ]
    )]
    public function getRestaurantReviews(int $id): JsonResponse
    {
        try {
            $reviews = $this->reviewService->getRestaurantReviews($id);
            return new JsonResponse(array_map(static fn($r) => new ReviewResponseDTO($r), $reviews), Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/reviews/users/{id}', methods: ['GET'])]
    #[OA\Get(
        path: "/reviews/users/{id}",
        summary: "Get reviews by a user",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of user reviews",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: new Model(type: ReviewResponseDTO::class)))
            ),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function getUserReviews(int $id): JsonResponse
    {
        try {
            $reviews = $this->reviewService->getUserReviews($id);
            return new JsonResponse(array_map(static fn($r) => new ReviewResponseDTO($r), $reviews), Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/reviews/restaurants/{id}/rating', methods: ['GET'])]
    #[OA\Get(
        path: "/reviews/restaurants/{id}/rating",
        summary: "Get average rating of a restaurant",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Average rating",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "averageRating", type: "number", format: "float", example: 4.5)
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Restaurant not found")
        ]
    )]
    public function getAverageRating(int $id): JsonResponse
    {
        try {
            $rating = $this->reviewService->getAverageRating($id);
            return new JsonResponse(['averageRating' => $rating ?? 0.0], Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}