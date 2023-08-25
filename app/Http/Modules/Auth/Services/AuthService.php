<?php

namespace App\Http\Modules\Auth\Services;

use App\Http\Modules\Auth\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use JWTAuth;

class AuthService
{
    public function __construct()
    {
    }

    /**
     * Function to login a user.
     *
     * @param LoginRequest $request
     * @return Array
     */
    public function login(LoginRequest $request): array
    {
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return [
                'res' => false,
                'message' => 'Credenciales incorrectas',
                'code' => Response::HTTP_UNAUTHORIZED,
                'data' => null,
            ];
        }
        $user = Auth::user();
        $user->getPermissionsViaRoles();
        $roles       = collect($user->roles->pluck('description'))->flatten()->toArray();
        $permissions = collect($user->getAllPermissions());
        $permissions = collect($permissions)->pluck('name')->flatten()->toArray();
        $userLogged = collect([
            'id'          => $user->id,
            'name'        => $user->name . ' ' . $user->last_name,
            'email'       => $user->email,
            'roles'       => base64_encode(json_encode($roles)),
            'permissions' => base64_encode(json_encode($permissions)),
        ]);
        return [
            'res' => true,
            'message' => 'Usuario logeado con éxito',
            'data' => [
                'token' => $token,
                'user' => $userLogged,
            ],
            'code' => Response::HTTP_OK,
        ];
    }

    /**
     * Function to logout user.
     *
     * @param Request $request
     * @return Array
     */
    public function logout(Request $request): array
    {
        try {
            JWTAuth::invalidate($request->token);
            return [
                'res' => true,
                'message' => 'Usuario desconectado',
                'code' => Response::HTTP_OK,
                'data' => null
            ];
        } catch (\Throwable $th) {
            return [
                'res' => false,
                'message' => $th->getMessage(),
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'data' => null
            ];
        }
    }
}
