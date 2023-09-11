<?php

namespace App\Http\Modules\Outputs\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\Outputs\Models\Output;

class OutputRepository extends RepositoryBase
{

    public function __construct(protected Output $OutputModel)
    {
        parent::__construct($OutputModel);
    }
}
