<?php

namespace App\Http\Modules\Users\Services;

use App\Http\Modules\RolesAndPermissions\Repositories\RoleRepository;
use App\Http\Modules\Users\Models\User;
use App\Http\Modules\Users\Repositories\UserRepository;
use App\Http\Modules\Users\Requests\ChangePasswordRequest;
use App\Http\Modules\Users\Requests\CreateUserRequest;
use App\Http\Modules\Users\Requests\UpdateUserRequest;
use Illuminate\Http\Response;

class UserService
{

    private $userRepository;
    private $roleRepository;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Function to store a new user.
     *
     * @param CreateUserRequest $request
     * @return Array
     */
    public function register(CreateUserRequest $request): array
    {
        $request['password'] = bcrypt($request['password']);
        $user                = new User($request->all());
        $user                = $this->userRepository->save($user);
        $user->syncRoles([]);
        $roles  = $this->roleRepository->getRolesByNames($request->role);
        $user->syncRoles($roles);

        if ($user) {
            return [
                'res'     => true,
                'message' => 'Usuario creado correctamente',
                'code'    => Response::HTTP_CREATED,
                'data'    => $user
            ];
        } else {
            return [
                'res'     => false,
                'message' => 'Error al crear usuario',
                'code'    => Response::HTTP_BAD_REQUEST,
                'data'    => null
            ];
        }
    }


    /**
     * Update a user
     *
     * @param  UpdateUserRequest $request
     * @param  int $id
     * @return Array
     * @author Luifer Almendrales
     */
    public function update(UpdateUserRequest $request, int $id): array
    {

        $user = $this->userRepository->find($id);

        if ($user) {
            if ($request->password) {
                $request->merge(['password' => bcrypt($request['password'])]);
            }
            $user->fill($request->all());
            $user   = $this->userRepository->save($user, $request);

            if ($request->role) {
                $user->syncRoles([]);
                $roles  = $this->roleRepository->getRolesByNames($request->role);
                $user->syncRoles($roles);
            }
            return [
                'res'     => true,
                'message' => 'Usuario actualizado correctamente',
                'code'    => Response::HTTP_CREATED,
                'data'    => $user
            ];
        } else {
            return [
                'res'     => false,
                'message' => 'Usuario no encontrado',
                'code'    => Response::HTTP_NOT_FOUND,
                'data'    => null
            ];
        }
    }

    /**
     * Change password of a user.
     *
     * @param  ChangePasswordRequest $request
     * @return Array
     * @author Luifer Almendrales
     */
    public function changePassword(ChangePasswordRequest $request): array
    {
        $user = $this->userRepository->find(auth()->user()->id);

        if ($user) {
            if (password_verify($request->password, $user->password)) {
                $user->password = bcrypt($request->new_password);
                $user           = $this->userRepository->save($user);
                return [
                    'res'     => true,
                    'message' => 'Contraseña actualizada correctamente',
                    'code'    => Response::HTTP_CREATED,
                    'data'    => $user
                ];
            } else {
                return [
                    'res'     => false,
                    'message' => 'Contraseña actual incorrecta',
                    'code'    => Response::HTTP_BAD_REQUEST,
                    'data'    => null
                ];
            }
        } else {
            return [
                'res'     => false,
                'message' => 'Usuario no encontrado',
                'code'    => Response::HTTP_NOT_FOUND,
                'data'    => null
            ];
        }
    }
}
