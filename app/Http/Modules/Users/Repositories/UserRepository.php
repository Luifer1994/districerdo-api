<?php

namespace App\Http\Modules\Users\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Users\Models\User;


class UserRepository extends RepositoryBase
{
    protected  $userModel;

    public function __construct(User $userModel)
    {
        parent::__construct($userModel);
        $this->userModel = $userModel;
    }

    /**
     * Get all users
     *
     * @param  int $limit
     * @param  string $search
     * @return Object
     * @author Luifer Almendrales
     */
    public function getAllUsers(int $limit, string $search): Object
    {
        return $this->userModel->select('id', 'name', 'last_name', 'email', 'is_active', 'document', 'document_type_id')
            ->selectRaw('CONCAT(name, " ", last_name) as full_name')
            ->when($search, function ($filter) use ($search) {
                $filter->where('name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('document', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->with(['document_type:id,name,code','roles:id,name,description'])
            ->orderBy('created_at', 'desc')
            ->paginate($limit);
    }

    /**
     * Get user by id
     *
     * @param  int $id
     * @return Object|null
     */
    public function getById(int $id): ?Object
    {
        return $this->userModel->select('id', 'name', 'last_name', 'email', 'is_active', 'document', 'document_type_id')
            ->selectRaw('CONCAT(name, " ", last_name) as full_name')
            ->with(['document_type:id,name,code', 'roles:id,name,description'])
            ->find($id);
    }
}
