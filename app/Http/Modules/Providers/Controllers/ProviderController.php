<?php

namespace App\Http\Modules\Providers\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Bases\PaginateBaseRequest;
use App\Http\Modules\Providers\Models\Provider;
use App\Http\Modules\Providers\Repositories\ProviderRepository;
use App\Http\Modules\Providers\Requests\CreateProviderRequest;
use App\Http\Modules\Providers\Requests\UpdateProviderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProviderController extends Controller
{
    protected $ProviderRepository;

    public function __construct(ProviderRepository $ProviderRepository)
    {
        $this->ProviderRepository = $ProviderRepository;
    }

    /**
     * Get all Providers.
     *
     * @param  PaginateBaseRequest $request
     * @return JsonResponse
     * @author Luifer Almendrales
     */
    public function index(PaginateBaseRequest $request): JsonResponse
    {
        try {
            $limit   = $request->limit ?? 10;
            $searhes = $request->search ?? '';
            $Providers = $this->ProviderRepository->getAllProviders($limit, $searhes);

            return $this->successResponse($Providers, 'Provideres listados con exito!');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get a Provider.
     *
     * @param  int $id
     * @return JsonResponse
     * @author Luifer Almendrales
     */
    public function show(int $id): JsonResponse
    {
        try {
            $Provider = $this->ProviderRepository->find($id);
            if (!$Provider)
                return $this->errorResponse('Providere no encontrado', Response::HTTP_NOT_FOUND);

            return $this->successResponse($Provider, 'Providere listado con exito!');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Create a new clent.
     *
     * @param  CreateProviderRequest $request
     * @return JsonResponse
     * @author Luifer Almendrales
     */
    public function store(CreateProviderRequest $request): JsonResponse
    {
        try {
            $Provider = new Provider($request->all());
            $Provider = $this->ProviderRepository->save($Provider);
            return $this->successResponse($Provider, 'Providere creado con exito!', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update a Provider.
     *
     * @param  UpdateProviderRequest $request
     * @return JsonResponse
     */
    public function update(UpdateProviderRequest $request, $id): JsonResponse
    {
        try {
            $Provider = $this->ProviderRepository->find($id);
            if (!$Provider)
                return $this->errorResponse('Providere no encontrado', Response::HTTP_NOT_FOUND);

            $Provider->fill($request->all());
            $Provider = $this->ProviderRepository->save($Provider);
            return $this->successResponse($Provider, 'Providere actualizado con exito!', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
