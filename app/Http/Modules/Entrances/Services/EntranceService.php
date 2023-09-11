<?php

namespace App\Http\Modules\Entrances\Services;

use App\Http\Modules\Entrances\Models\Entrance;
use App\Http\Modules\Entrances\Repositories\EntranceRepository;

class EntranceService
{

    public function __construct(protected EntranceRepository $EntranceRepository)
    {
    }
}
