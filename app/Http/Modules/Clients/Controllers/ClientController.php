<?php

namespace App\Http\Modules\Clients\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Bases\PaginateBaseRequest;
use App\Http\Modules\Clients\Models\Client;
use App\Http\Modules\Clients\Repositories\ClientRepository;
use App\Http\Modules\Clients\Requests\CreateClientRequest;
use App\Http\Modules\Clients\Requests\UpdateClientRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    protected $ClientRepository;

    public function __construct(ClientRepository $ClientRepository)
    {
        $this->ClientRepository = $ClientRepository;
    }

    /**
     * Get all clients.
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
            $clients = $this->ClientRepository->getAllClients($limit, $searhes);

            return $this->successResponse($clients, 'Clientes listados con exito!');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get a client.
     *
     * @param  int $id
     * @return JsonResponse
     * @author Luifer Almendrales
     */
    public function show(int $id): JsonResponse
    {
        try {
            $client = $this->ClientRepository->find($id);
            if (!$client)
                return $this->errorResponse('Cliente no encontrado', Response::HTTP_NOT_FOUND);

            return $this->successResponse($client, 'Cliente listado con exito!');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Create a new clent.
     *
     * @param  CreateClientRequest $request
     * @return JsonResponse
     * @author Luifer Almendrales
     */
    public function store(CreateClientRequest $request): JsonResponse
    {
        try {
            $client = new Client($request->all());
            $client = $this->ClientRepository->save($client);
            return $this->successResponse($client, 'Cliente creado con exito!', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update a client.
     *
     * @param  UpdateClientRequest $request
     * @return JsonResponse
     */
    public function update(UpdateClientRequest $request, $id): JsonResponse
    {
        try {
            $client = $this->ClientRepository->find($id);
            if (!$client)
                return $this->errorResponse('Cliente no encontrado', Response::HTTP_NOT_FOUND);

            $client->fill($request->all());
            $client = $this->ClientRepository->save($client);
            return $this->successResponse($client, 'Cliente actualizado con exito!', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
