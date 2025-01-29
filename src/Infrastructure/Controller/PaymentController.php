<?php

namespace App\Infrastructure\Controller;

use App\Domain\Enum\PaymentMethodEnum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\Port\PaymentUseCaseInterface;
use App\Application\DTO\Payment\PaymentRequestDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\DTO\Payment\PaymentResponseDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

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
    public function processPayment(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

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