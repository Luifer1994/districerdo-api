<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse
{

    /**
     * Success response
     *
     * @param mixed $data
     * @param string $message
     * @param integer $code
     * @return JsonResponse
     */
    protected function successResponse(mixed $data, string $message = "Ok", int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status'  => 'Success',
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    /**
     * Error response
     *
     * @param string|null $message
     * @param integer $code
     * @return JsonResponse
     */
    protected function errorResponse(string $message = null, int $code): JsonResponse
    {
        return response()->json([
            'status'  => 'Error',
            'message' => $message,
            'data'    => null
        ], $code);
    }
}
