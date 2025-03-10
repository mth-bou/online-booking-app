<?php

namespace App\Infrastructure\Controller;

use App\Domain\Enum\PaymentMethodEnum;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Application\Port\PaymentUseCaseInterface;
use App\Application\DTO\Payment\PaymentRequestDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\DTO\Payment\PaymentResponseDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Payments")]
class PaymentController extends AbstractController
{
    private PaymentUseCaseInterface $paymentService;
    private ValidatorInterface $validator;

    public function __construct(
        PaymentUseCaseInterface $paymentService,
        ValidatorInterface $validator
    ) {
        $this->paymentService = $paymentService;
        $this->validator = $validator;
    }

    #[Route('/payments', methods: ['POST'])]
    #[OA\Post(
        path: "/payments",
        summary: "Process a new payment",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: new Model(type: PaymentRequestDTO::class))
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Payment successfully processed",
                content: new OA\JsonContent(ref: new Model(type: PaymentResponseDTO::class))
            ),
            new OA\Response(response: 400, description: "Invalid input data"),
            new OA\Response(response: 404, description: "Reservation not found")
        ]
    )]
    public function processPayment(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['paymentMethod']) || !PaymentMethodEnum::isValid($data['paymentMethod'])) {
            return new JsonResponse(['error' => 'Invalid payment method.'], Response::HTTP_BAD_REQUEST);
        }

        $dto = new PaymentRequestDTO(
            $data['reservationId'] ?? 0,
            $data['amount'] ?? 0,
            $data['paymentMethod']
        );
        
        $errors = $this->validator->validate($dto);

        if ($errors->count() > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $payment = $this->paymentService->processPayment(
                $dto->reservationId,
                $dto->amount,
                $dto->paymentMethod
            );
            return new JsonResponse(new PaymentResponseDTO($payment), Response::HTTP_CREATED);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/payments/{id}/confirm', methods: ['PATCH'])]
    #[OA\Patch(
        path: "/payments/{id}/confirm",
        summary: "Confirm a payment",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Payment confirmed"),
            new OA\Response(response: 404, description: "Payment not found")
        ]
    )]
    public function confirmPayment(int $id): JsonResponse
    {
        try {
            $this->paymentService->confirmPayment($id);
            return new JsonResponse(['message' => 'Payment confirmed.'], Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/payments/{id}/refund', methods: ['PATCH'])]
    #[OA\Patch(
        path: "/payments/{id}/refund",
        summary: "Refund a payment",
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Payment refunded"),
            new OA\Response(response: 404, description: "Payment not found")
        ]
    )]
    public function refundPayment(int $id): JsonResponse
    {
        try {
            $this->paymentService->refundPayment($id);
            return new JsonResponse(['message'=> 'Payment refunded.'], Response::HTTP_OK);
        } catch (NotFoundResourceException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}