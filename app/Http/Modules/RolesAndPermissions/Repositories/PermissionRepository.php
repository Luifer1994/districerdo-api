<?php

namespace App\Http\Modules\RolesAndPermissions\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;

class PermissionRepository
{
    protected $model;
    function __construct(Permission $permission)
    {
        $this->model = $permission;
    }

    /**
     * Funtion to get permissions by array of ids.
     *
     * @param array $ids
     * @return object
     */
    public function getPermissionsByIds(array $ids, string $group): object
    {
        return $this->model->select('id', 'name', 'description', 'group')
            ->where('group', $group)
            ->whereIn('id', $ids)
            ->get();
    }

    /**
     * Funtion to get all permissions in group by group and mark the permissions that the role has no ussing foreach.
     *
     * @return object
     */
    public function getAllPermissionsInGroupByGroup(int $role_id): object
    {
        return $this->model->select('id', 'name', 'description', 'group')
            ->orderBy('group', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->groupBy('group');
    }

    /**
     * Funtion to get groups of permissions.
     *
     * string $group
     * @param int $limit
     * @return Collection
     */
    public function getGroupPermissionsByGroup(string $group, int $limit): Collection
    {
        return $this->model->select('group')
            ->where('group', 'like', "%{$group}%")
            ->limit($limit)
            ->distinct()->get();
    }

    /**
     * Funtion to get permissions and relationship to role by group.
     *
     * @param string $group
     * @param int $rolId
     * @return Collection
     */
    public function getPermissionsByGroup(string $group, $rolId): Collection
    {
        return $this->model->select('id', 'name', 'description', 'group')
            ->where('group', $group)
            /* ->withCount(['roles' => function ($query) use ($rolId) {
                $query->select('id', 'name')->where('id', $rolId);
            }]) */
            /* ->selectRaw('IF((SELECT COUNT(*) FROM role_has_permissions WHERE role_has_permissions.permission_id = permissions.id AND role_has_permissions.role_id = ?) > 0, 1, 0) AS has_permission', [$rolId]) */
            ->selectRaw('CASE WHEN (SELECT COUNT(*) FROM role_has_permissions WHERE role_has_permissions.permission_id = permissions.id AND role_has_permissions.role_id = ?) > 0 THEN true ELSE false END AS checked', [$rolId])
            ->orderBy('id', 'ASC')
            ->get();
    }

    /**
     * Funtion to get permissions by group and role.
     *
     * @param string $group
     * @param int $rolId
     * @return Collection
     */
    public function getPermissionsByGroupAndRole(string $group, int $rolId): Collection
    {
        return $this->model->select('id', 'name', 'description', 'group')
            ->where('group', $group)
            ->whereHas('roles', function ($query) use ($rolId) {
                $query->where('id', $rolId);
            })
            ->orderBy('id', 'ASC')
            ->get();
    }
}
