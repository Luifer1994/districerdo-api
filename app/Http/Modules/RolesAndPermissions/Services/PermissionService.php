<?php

namespace App\Http\Modules\RolesAndPermissions\Services;

use App\Http\Modules\RolesAndPermissions\Repositories\PermissionRepository;
use App\Http\Modules\RolesAndPermissions\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionService
{

    protected   $PermissionRepository;
    protected   $RoleRepository;

    public function __construct(PermissionRepository $PermissionRepository, RoleRepository $RoleRepository)
    {
        $this->PermissionRepository = $PermissionRepository;
        $this->RoleRepository       = $RoleRepository;
    }

    /**
     * Funtion to return al permissions in group by group and mark the permissions that the role has.
     *
     * @param int $role_id
     * @return object
     */
    public function getAllPermissionsInGroupByGroup(int $role_id): object
    {
        try {
            $permissions = $this->PermissionRepository->getAllPermissionsInGroupByGroup($role_id);
            $role        = $this->RoleRepository->getById($role_id);

            foreach ($permissions as $group => $permission) {
                foreach ($permission as $key => $value) {
                    $permissions[$group][$key]['has'] = $role->hasPermissionTo($value->name);
                }
            }

            return (object) ['res' => true, 'data' => $permissions, 'message' => 'Ok', 'code' => Response::HTTP_OK];
        } catch (\Throwable $th) {
            return (object) ['res' => false, 'data' => null, 'message' => $th->getMessage(), 'code' => Response::HTTP_BAD_REQUEST];
        }
    }
}
