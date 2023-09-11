<?php

namespace App\Console\Commands\Permissions\Providers;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProviderPermissionsManyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-permission-providers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Createa all permissions for providers module';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->line('Creando permisos de proveedores');
        try {
            $permisions = [
                [
                    'name'        => 'providers-module',
                    'description' => 'Módulo de Proveedores',
                    'group'       => 'Proveedores'
                ],
                [
                    "name"        => 'providers-create',
                    "description" => 'Crear Proveedores',
                    "group"       => 'Proveedores'
                ],
                [
                    "name"        => 'providers-list',
                    "description" => 'Listar Proveedores',
                    "group"       => 'Proveedores'
                ],
                [
                    "name"        => 'providers-show',
                    "description" => 'Ver Proveedores',
                    "group"       => 'Proveedores'
                ],
                [
                    "name"        => 'providers-update',
                    "description" => 'Actualizar Proveedores',
                    "group"       => 'Proveedores'
                ],
                [
                    "name"        => 'providers-search',
                    "description" => 'Buscar Proveedores',
                    "group"       => 'Proveedores'
                ],
            ];
            $role        = Role::where('name', 'admin')->first();
            foreach ($permisions as  $value) {
                $permission = Permission::firstOrCreate($value);
                $role->givePermissionTo($permission);
            }
            $this->info('Permisos de proveedores creados con exito!');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }

    }
}
