<?php

namespace App\Console\Commands\Permissions\Product;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProductPermissionsManyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-permission-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Createa all permissions for products module';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->line('Creando permisos de Productos...');
        try {
            $permisions = [
                [
                    'name'        => 'products-module',
                    'description' => 'Módulo de Productos',
                    'group'       => 'Productos',
                ],
                [
                    "name"        => 'products-create',
                    "description" => 'Crear Productos',
                    "group"       => 'Productos',
                ],
                [
                    "name"        => 'products-list',
                    "description" => 'Listar Productos',
                    "group"       => 'Productos',
                ],
                [
                    "name"        => 'products-show',
                    "description" => 'Ver Productos',
                    "group"       => 'Productos',
                ],
                [
                    "name"        => 'products-destroy',
                    "description" => 'Eliminar Productos',
                    "group"       => 'Productos',
                ],
                [
                    "name"        => 'products-update',
                    "description" => 'Actualizar Productos',
                    "group"       => 'Productos',
                ],
                [
                    "name"        => 'products-search',
                    "description" => 'Buscar Productos',
                    "group"       => 'Productos',
                ],
            ];
            $role        = Role::where('name', 'admin')->first();
            foreach ($permisions as  $value) {
                $permission = Permission::firstOrCreate($value);
                $role->givePermissionTo($permission);
            }
            $this->info('Permisos creados correctamente');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }
}
