<?php

namespace App\Http\Modules\RolesAndPermissions\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\RolesAndPermissions\Repositories\RoleRepository;
use App\Http\Modules\RolesAndPermissions\Requests\AsingPermissionsToRoleRequest;
use App\Http\Modules\RolesAndPermissions\Requests\CreateRoleRequest;
use App\Http\Modules\RolesAndPermissions\Requests\UpdateRoleRequest;
use App\Http\Modules\RolesAndPermissions\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RoleController extends Controller
{

    protected $RoleRepository;
    protected $RoleService;

    public function __construct(RoleRepository $RoleRepository, RoleService $RoleService)
    {
        $this->RoleRepository = $RoleRepository;
        $this->RoleService    = $RoleService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            return $this->successResponse($this->RoleRepository->getAll(), 'ok', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Function to show a role by id.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $role = $this->RoleRepository->getById($id);
            if (!$role) {
                return $this->errorResponse('No se encontró el rol', Response::HTTP_NOT_FOUND);
            }
            return $this->successResponse($role, 'ok', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * Asing permissions to a role.
     *
     * @param AsingPermissionsToRoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function asingPermissionsToRole(AsingPermissionsToRoleRequest $request): JsonResponse
    {
        try {
            $role = $this->RoleService->asingPermissionsToRole($request->role_id, $request->permissions, $request->group);
            return $this->successResponse($role->data, $role->message, $role->code);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Find created new role.
     *
     * @param CreateRoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRoleRequest $request): JsonResponse
    {
        try {
            $role = $this->RoleRepository->create($request->all());
            return $this->successResponse($role->data, 'Rol creado con éxito', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Function update role.
     *
     * @param UpdateRoleRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRoleRequest $request, int $id): JsonResponse
    {
        try {
            $role = $this->RoleRepository->getById($id);
            if (!$role) {
                return $this->errorResponse('No se encontró el rol', Response::HTTP_NOT_FOUND);
            }
            $role = $this->RoleRepository->update($request->all());
            return $this->successResponse($role, 'Rol actualizado con éxito', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
