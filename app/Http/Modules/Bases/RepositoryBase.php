<?php

namespace App\Http\Modules\Bases;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class RepositoryBase {

    function __construct(
        protected Model $model
    ) { }

    /**
     * Find one row.
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id) : ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Save and return saved model.
     *
     * @param Model $model
     * @return Model|null
     */
    public function save(Model $model) : ?Model
    {
        $model->save();
        return $model;
    }

    /**
     * Get by where params.
     *
     * @param array $where
     * @return Collection
     */
    public function get(array $where = []) : Collection
    {
        $query = $this->model;
        if (!empty($where)) {
            $query->where($where);
        }
        return $query->get();
    }
}
