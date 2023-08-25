<?php

namespace App\Console\Commands\Permissions\Users;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserPermissionsManyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-permission-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Createa all permissions for users module';

    /**
     * Execute the console command.
     *
    * @return void
     */
    public function handle(): void
    {
        $this->line('Creando permisos de usuarios...');
        try {
            $permisions = [
                [
                    'name'        => 'users-module',
                    'description' => 'Módulo de usuarios',
                    'group'       => 'Usuarios',
                ],
                [
                    "name"        => 'users-create',
                    "description" => 'Crear usuarios',
                    "group"       => 'Usuarios',
                ],
                [
                    "name"        => 'users-list',
                    "description" => 'Listar usuarios',
                    "group"       => 'Usuarios',
                ],
                [
                    "name"        => 'users-show',
                    "description" => 'Ver usuarios',
                    "group"       => 'Usuarios',
                ],
                [
                    "name"        => 'users-destroy',
                    "description" => 'Eliminar usuarios',
                    "group"       => 'Usuarios',
                ],
                [
                    "name"        => 'users-update',
                    "description" => 'Actualizar usuarios',
                    "group"       => 'Usuarios',
                ]
            ];
            $role        = Role::where('name', 'admin')->first();
            foreach ($permisions as  $value) {
                $permission = Permission::firstOrCreate($value);
                $role->givePermissionTo($permission);
            }
            $this->info('Permisos de usuarios creados correctamente');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }

    }
}
