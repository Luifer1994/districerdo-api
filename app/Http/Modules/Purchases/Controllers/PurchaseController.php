<?php

namespace App\Http\Modules\Purchases\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Bases\PaginateBaseRequest;
use App\Http\Modules\Purchases\Repositories\PurchaseRepository;
use App\Http\Modules\Purchases\Requests\CreateOrUpdatePurchaseRequest;
use App\Http\Modules\Purchases\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    public function __construct(protected PurchaseRepository $PurchaseRepository, protected PurchaseService $PurchaseService)
    {
    }

    /**
     * Get all purchases paginated.
     *
     * @param PaginateBaseRequest $request
     * @return JsonResponse
     * @author Luifer Almendrales
     */
    public function index(PaginateBaseRequest $request): JsonResponse
    {
        try {
            $limit = $request->limit ?? 10;
            $search = $request->search ?? '';
            $state = $request->state ?? '';
            $dateStar = $request->date_start ?? '';
            $dateEnd = $request->date_end ?? '';
            $data = $this->PurchaseRepository->list($limit, $search, $state, $dateStar, $dateEnd);

            return $this->successResponse($data, 'Compras listadas correctamente');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * Create new Purchase.
     *
     * @param  CreateOrUpdatePurchaseRequest $request
     * @return JsonResponse
     */
    public function store(CreateOrUpdatePurchaseRequest $request): JsonResponse
    {
        try {
            $newPurchase = $this->PurchaseService->createPurchase($request);

            if ($newPurchase['res'])
                return $this->successResponse($newPurchase['data'], $newPurchase['message'], Response::HTTP_CREATED);
            else
                return $this->errorResponse($newPurchase['message'], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al crear compra', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Find a Purchase by code.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $purchase = $this->PurchaseRepository->findById($id);
            if ($purchase)
                return $this->successResponse($purchase, 'Compra encontrada correctamente');
            else
                return $this->errorResponse('Compra no encontrada', Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al buscar compra', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * paid Purchase.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function paid(int $id): JsonResponse
    {
        try {
            $paidPurchase = $this->PurchaseService->paidPurchase($id);
            if ($paidPurchase['res'])
                return $this->successResponse(null, $paidPurchase['message'], Response::HTTP_CREATED);
            else
                return $this->errorResponse($paidPurchase['message'], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return $this->errorResponse('Error al pagar compra', Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get total amount for month.
     *
     * @return JsonResponse
     */
    function totalAmountForMonth(): JsonResponse
    {
        try {
            $data = $this->PurchaseRepository->totalAmountForMonth();
            return $this->successResponse($data);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
