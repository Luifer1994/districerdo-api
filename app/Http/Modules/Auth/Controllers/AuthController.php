<?php

namespace App\Http\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Modules\Auth\Requests\LoginRequest;
use App\Http\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $AuthService;

    public function __construct(AuthService $AuthService)
    {
        $this->AuthService = $AuthService;
    }

    /**
     * Function to login a user.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @author Luis Almendrales
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $login = $this->AuthService->login($request);
        if (!$login['res']) {
            return  $this->errorResponse($login['message'], $login['code']);
        }
        return $this->successResponse($login['data'], $login['message'], $login['code']);
    }

    /**
     * Function to logout user.
     *
     * @param Request $request (token)
     * @return JsonResponse
     * @author Luis Almendrales
     */
    public function logout(Request $request): JsonResponse
    {
        $logout = $this->AuthService->logout($request);
        if (!$logout['res']) {
            return  $this->errorResponse($logout['message'], $logout['code']);
        }
        return $this->successResponse($logout['data'], $logout['message'], $logout['code']);
    }
}
