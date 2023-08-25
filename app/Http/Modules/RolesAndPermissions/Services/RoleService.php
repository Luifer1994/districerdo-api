<?php

namespace App\Http\Modules\RolesAndPermissions\Services;

use App\Http\Modules\RolesAndPermissions\Repositories\PermissionRepository;
use App\Http\Modules\RolesAndPermissions\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleService
{
    protected   $RoleRepository;
    protected   $PermissionRepository;

    public function __construct(RoleRepository $RoleRepository, PermissionRepository $PermissionRepository)
    {
        $this->RoleRepository       = $RoleRepository;
        $this->PermissionRepository = $PermissionRepository;
    }

    /**
     * Funtion to asing permissions to a role.
     *
     * @param int $role_id
     * @param array $permissions_ids
     * @param string $group
     * @return object
     */
    public function asingPermissionsToRole(int $role_id, array $permissions_ids, string $group): object
    {
        try {
            $role        = $this->RoleRepository->getById($role_id);
            $permissionsRemove = $this->PermissionRepository->getPermissionsByGroupAndRole($group, $role_id);
            $role->revokePermissionTo($permissionsRemove);
            $permissions = $this->PermissionRepository->getPermissionsByIds($permissions_ids, $group);
            $permissions->each(function ($permission) use ($role) {
                $role->givePermissionTo($permission->name);
            });
            return (object) ['res' => true, 'data' => $role, 'message' => 'Permisos asignados correctamente¡', 'code' => Response::HTTP_CREATED];
        } catch (\Throwable $th) {
            return (object) ['res' => false, 'data' => null, 'message' => $th->getMessage(), 'code' => Response::HTTP_BAD_REQUEST];
        }
    }
}
