<?php

namespace App\Http\Modules\Cities\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Cities\Models\City;

class CityRepository extends RepositoryBase
{
    protected  $CityModel;

    public function __construct(City $CityModel)
    {
        parent::__construct($CityModel);
        $this->CityModel = $CityModel;
    }

    /**
     * Get all cities.
     *
     * @param  int $limit
     * @param  string $search
     * @return object
     * @author Luifer Almendrales
     */
    public function getCities($limit, $search): object
    {
        return $this->CityModel
            ->select('id', 'name','department_id')
            ->with(['department:id,name'])
            ->where('name', 'like', "%$search%")
            ->paginate($limit);
    }
}
