<?php

namespace App\Http\Modules\RolesAndPermissions\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Bases\PaginateBaseRequest;
use App\Http\Modules\RolesAndPermissions\Repositories\PermissionRepository;
use App\Http\Modules\RolesAndPermissions\Services\PermissionService;
use Illuminate\Http\{Response, JsonResponse};

class PermissionController extends Controller
{

    protected $PermissionRepository;
    protected $PermissionService;

    public function __construct(PermissionRepository $PermissionRepository, PermissionService $PermissionService)
    {
        $this->PermissionRepository = $PermissionRepository;
        $this->PermissionService    = $PermissionService;
    }

    /**
     * Funtion to get groups the permissions by group.
     *
     * @param PaginateBaseRequest $request
     * @return JsonResponse
     */
    public function getGroupPermissionsByGroup(PaginateBaseRequest $request): JsonResponse
    {
        try {
            $limit = $request->limit ?? 10;
            $searhes = $request->search ?? '';
            $permissions = $this->PermissionRepository->getGroupPermissionsByGroup($searhes, $limit);
            return $this->successResponse($permissions, 'ok', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Funtion to get permissions by group and mark if the permission is assigned to the role.
     *
     * @param string $group
     * @param int $rolId
     * @return JsonResponse
     */
    public function getPermissionsByGroup(string $group, int $rolId): JsonResponse
    {
        try {
            $permissions = $this->PermissionRepository->getPermissionsByGroup($group, $rolId);
            return $this->successResponse($permissions, 'ok', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
