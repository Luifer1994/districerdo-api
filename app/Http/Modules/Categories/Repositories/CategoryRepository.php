<?php

namespace App\Http\Modules\Categories\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Categories\Models\Category;

class CategoryRepository extends RepositoryBase
{
    protected  $CategoryModel;

    public function __construct(Category $CategoryModel)
    {
        parent::__construct($CategoryModel);
        $this->CategoryModel = $CategoryModel;
    }
}
