<?php

namespace App\Http\Modules\Invoices\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Bases\PaginateBaseRequest;
use App\Http\Modules\Invoices\Repositories\InvoiceRepository;
use App\Http\Modules\Invoices\Requests\CreateOrUpdateInvoiceRequest;
use App\Http\Modules\Invoices\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    protected $InvoiceRepository, $InvoiceService;

    public function __construct(InvoiceRepository $InvoiceRepository, InvoiceService $InvoiceService)
    {
        $this->InvoiceRepository = $InvoiceRepository;
        $this->InvoiceService = $InvoiceService;
    }

    /**
     * Get all document types.
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
            $data = $this->InvoiceRepository->getAllInvoices($limit, $search, $state, $dateStar, $dateEnd);

            return $this->successResponse($data, 'Facturas listadas com éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Create new Invoice.
     *
     * @param CreateOrUpdateInvoiceRequest $request
     * @return JsonResponse
     */
    function store(CreateOrUpdateInvoiceRequest $request) //: JsonResponse
    {
        try {
            $data = $this->InvoiceService->CreateInvoice($request);
            if (!$data->status)
                return $this->errorResponse($data->message, Response::HTTP_BAD_REQUEST);

            return $this->successResponse($data->data, $data->message, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get Invoice by id.
     *
     * @param int $id
     * @return JsonResponse
     */
    function show(int $id): JsonResponse
    {
        try {
            $data = $this->InvoiceRepository->getInvoiceById($id);
            if (!$data)
                return $this->errorResponse('Factura no encontrada', Response::HTTP_NOT_FOUND);

            return $this->successResponse($data, 'Factura encontrada');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Paid Invoice.
     *
     * @param int $id
     * @return JsonResponse
     */
    function paid(int $id): JsonResponse
    {
        try {
            $data = $this->InvoiceRepository->find($id);
            if (!$data)
                return $this->errorResponse('Factura no encontrada', Response::HTTP_NOT_FOUND);

            if ($data->state == 'PAID')
                return $this->errorResponse('La factura ya se encuentra pagada', Response::HTTP_BAD_REQUEST);

            if ($data->state == 'CANCELLED')
                return $this->errorResponse('La factura se encuentra cancelada', Response::HTTP_BAD_REQUEST);

            $data->state = 'PAID';
            return $this->successResponse($this->InvoiceRepository->save($data), 'Factura pagada con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Cancel Invoice.
     *
     * @param int $id
     * @return JsonResponse
     */
    function cancel(int $id): JsonResponse
    {
        try {
            $data = $this->InvoiceService->cancelInvoice($id);
            if (!$data->status)
                return $this->errorResponse($data->message, Response::HTTP_BAD_REQUEST);

            return $this->successResponse($data->data, $data->message);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Download Invoice.
     *
     * @param int $id
     * @return JsonResponse
     */
    function download(int $id) //: JsonResponse
    {
        try {
            $data = $this->InvoiceService->downloadInvoice($id);

            if (!$data['status'])
                return $this->errorResponse($data['message'], Response::HTTP_BAD_REQUEST);

            return $this->successResponse($data['data'], $data['message']);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
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
            $data = $this->InvoiceRepository->totalAmountForMonth();
            return $this->successResponse($data);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
