<?php

namespace App\Http\Modules\Cities\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Bases\PaginateBaseRequest;
use App\Http\Modules\Cities\Repositories\CityRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CityController extends Controller
{
    protected $CityRepository;

    public function __construct(CityRepository $CityRepository)
    {
        $this->CityRepository = $CityRepository;
    }

    /**
     * Get all document types.
     *
     * @param  PaginateBaseRequest $request
     * @return JsonResponse
     * @author Luifer Almendrales
     */
    public function index(PaginateBaseRequest $request): JsonResponse
    {
        try {
            $limit  = $request->limit ?? 10;
            $search = $request->search ?? '';

            return $this->successResponse($this->CityRepository->getCities($limit, $search), 'Ciudades listadas con exito!');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
