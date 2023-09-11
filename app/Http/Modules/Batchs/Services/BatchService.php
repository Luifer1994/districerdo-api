<?php

namespace App\Http\Modules\Batchs\Services;

use App\Http\Modules\Batchs\Repositories\BatchRepository;
use App\Traits\GenerateCodeRandom;

class BatchService
{
    use GenerateCodeRandom;

    public function __construct(protected BatchRepository $BatchRepository)
    {
    }

    /**
     * Generate code unique for Batch.
     *
     * return string
     */
    public function generateCodeUnique($length): string
    {
        $code = $this->generateCode($length);
        if ($this->BatchRepository->findByCode($code)) {
            return $this->generateCode($length); // Return the result of the recursive call
        } else {
            return $code;
        }
    }
}
