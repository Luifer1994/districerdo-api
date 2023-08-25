<?php

namespace App\Console\Commands\Permissions\RolesAndPermission;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsManyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-permission-roles-and-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Createa all permissions for roles and permissions module';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->line('Creando permisos de roles y permisos...');
        try {
            $permisions = [
                [
                    'name'        => 'configuration',
                    'description' => 'Configuración',
                    'group'       => 'Roles y permisos',
                ],
                [
                    'name'        => 'module-roles-and-permissions',
                    'description' => 'Módulo de roles y permisos',
                    'group'       => 'Roles y permisos',
                ],
                [
                    'name'        => 'list-roles',
                    'description' => 'Listar roles',
                    'group'       => 'Roles y permisos',
                ],
                [
                    'name'        => 'show-roles',
                    'description' => 'Ver rol',
                    'group'       => 'Roles y permisos'
                ],
                [
                    'name'        => 'create-roles',
                    'description' => 'Crear rol',
                    'group'       => 'Roles y permisos'
                ],
                [
                    'name'        => 'delete-roles',
                    'description' => 'Eliminar rol',
                    'group'       => 'Roles y permisos'
                ],
                [
                    'name'        => 'update-roles',
                    'description' => 'Actualizar rol',
                    'group'       => 'Roles y permisos'
                ],
                [
                    'name'        => 'asisgn-permissions-to-roles',
                    'description' => 'Asignar permisos a rol',
                    'group'       => 'Roles y permisos'
                ],
                [
                    'name'        => 'list-groups-to-permissions',
                    'description' => 'Listar grupos de permisos',
                    'group'       => 'Roles y permisos'
                ],
                [
                    'name'        => 'list-permissions-by-group',
                    'description' => 'Listar permisos por grupo',
                    'group'       => 'Roles y permisos'
                ]
            ];
            $role        = Role::where('name', 'admin')->first();
            foreach ($permisions as  $value) {
                $permission = Permission::firstOrCreate($value);
                $role->givePermissionTo($permission);
            }
            $this->info('Permisos de roles y permisos creados correctamente');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }
}
