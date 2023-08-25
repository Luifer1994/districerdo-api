<?php

namespace App\Http\Modules\RolesAndPermissions\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class RoleRepository
{
    protected $model;
    function __construct(Role $role)
    {
        $this->model = $role;
    }

    /**
     * Funtion to get all roles.
     *
     * @return Object
     */
    public function getAll(): Object
    {
        return $this->model->select('id', 'name', 'description')
            ->withCount('permissions')
            ->get();
    }

    /**
     * Funtion to get a role by id.
     *
     * @param int $id
     * @return Object
     */
    public function getById(int $id): Object
    {
        return $this->model->select('id', 'name', 'description', 'guard_name')->findOrFail($id);
    }

    /**
     * Funtion to get a roles by names.
     *
     * @param string $name
     * @return collection
     */
    public function getRolesByNames(string $name): Collection
    {
        return $this->model->select('*')
            ->where('name', $name)
            ->get();
    }

    /**
     * Funtion created new role.
     *
     * @param array $data
     * @return Object
     */
    public function create(array $data): Object
    {
        return $this->model->create($data);
    }

    /**
     * Funtion to update a role.
     *
     * @param array $data
     * @return Bool
     */
    public function update(array $data): Bool
    {
        return $this->model->update($data);
    }
}
