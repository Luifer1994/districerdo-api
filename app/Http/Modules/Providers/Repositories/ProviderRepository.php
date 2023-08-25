<?php

namespace App\Http\Modules\Providers\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Providers\Models\Provider;

class ProviderRepository extends RepositoryBase
{
    protected  $ProviderModel;

    public function __construct(Provider $ProviderModel)
    {
        parent::__construct($ProviderModel);
        $this->ProviderModel = $ProviderModel;
    }

    /**
     * Get all Providers.
     *
     * @param  int $limit
     * @param  string $search
     * @return object
     * @author Luifer Almendrales
     */
    public function getAllProviders($limit, $search): object
    {
        return $this->ProviderModel
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
     * Get a Provider.
     *
     * @param  int $id
     * @return object|null
     */
    public function getProvider(int $id): ?object
    {
        return $this->ProviderModel
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
