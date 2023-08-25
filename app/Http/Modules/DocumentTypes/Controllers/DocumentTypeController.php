<?php

namespace App\Http\Modules\DocumentTypes\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\DocumentTypes\Repositories\DocumentTypeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DocumentTypeController extends Controller
{
    protected $DocumentTypeRepository;

    public function __construct(DocumentTypeRepository $DocumentTypeRepository)
    {
        $this->DocumentTypeRepository = $DocumentTypeRepository;
    }

    /**
     * Get all document types.
     *
     * @return JsonResponse
     * @author Luifer Almendrales
     */
    public function index(): JsonResponse
    {
        try {
            $data = $this->DocumentTypeRepository->getAllDocumentTypes();

            return $this->successResponse($data, 'Tipos de documentos listados con exito!');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
