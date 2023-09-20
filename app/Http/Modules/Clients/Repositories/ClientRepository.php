<?php

namespace App\Http\Modules\Clients\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Clients\Models\Client;

class ClientRepository extends RepositoryBase
{
    protected  $clientModel;

    public function __construct(Client $clientModel)
    {
        parent::__construct($clientModel);
        $this->clientModel = $clientModel;
    }

    /**
     * Get all clients.
     *
     * @param  int $limit
     * @param  string $search
     * @return object
     * @author Luifer Almendrales
     */
    public function getAllClients($limit, $search): object
    {
        return $this->clientModel
            ->select('id', 'name', 'last_name', 'email', 'phone', 'document_number', 'address', 'document_type_id', 'city_id')
            ->selectRaw('CONCAT(name, " ", last_name) as full_name')
            ->with(['DocumentType:id,name,code', 'City' => function ($query) {
                $query->select('id', 'name','department_id')
                    ->with(['department' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->where('name', 'like', "%$search%")
            ->orWhere('last_name', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->orWhere('phone', 'like', "%$search%")
            ->orWhere('document_number', 'like', "%$search%")
            ->orderBy('id', 'desc')
            ->paginate($limit);
    }

    /**
     * Search clients by name or last name or document.
     *
     * @param  string $search
     * @return object
     */
    public function searchClients(string $search): object
    {
        return $this->clientModel
            ->select('id', 'name', 'last_name', 'email', 'phone', 'document_number', 'address', 'document_type_id', 'city_id')
            ->selectRaw('CONCAT(name, " ", last_name) as full_name')
            ->with(['DocumentType:id,name,code', 'City' => function ($query) {
                $query->select('id', 'name','department_id')
                    ->with(['department' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->where('name', 'like', "%$search%")
            ->orWhere('last_name', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->orWhere('phone', 'like', "%$search%")
            ->orWhere('document_number', 'like', "%$search%")
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get a client.
     *
     * @param  int $id
     * @return object|null
     */
    public function getClient(int $id): ?object
    {
        return $this->clientModel
            ->select('id', 'name', 'last_name', 'email', 'phone', 'document_number', 'type', 'address', 'document_type_id', 'city_id')
            ->selectRaw('CONCAT(name, " ", last_name) as full_name')
            ->selectRaw('CASE WHEN type = "natural" THEN "Persona Natural" ELSE "Persona Jurídica" END as type')
            ->with(['document_type:id,name', 'city' => function ($query) {
                $query->select('id', 'name')
                    ->with(['department' => function ($query) {
                        $query->select('id', 'name');
                    }]);
            }])
            ->where('id', $id)
            ->first();
    }
}
