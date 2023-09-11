<?php

namespace App\Http\Modules\Invoices\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Bases\PaginateBaseRequest;
use App\Http\Modules\Invoices\Repositories\InvoiceRepository;
use App\Http\Modules\Invoices\Requests\CreateOrUpdateInvoiceRequest;
use App\Http\Modules\Invoices\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    protected $InvoiceRepository,$InvoiceService;

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
}
