<?php

namespace App\Console\Commands\Permissions\Purchases;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PurchasePermissionsManyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-permission-purchases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Createa all permissions for purchases module';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->line('Creando permisos de compras');
        try {
            $permisions = [
                [
                    'name'        => 'purchases-module',
                    'description' => 'Módulo de compras',
                    'group'       => 'Compras'
                ],
                [
                    "name"        => 'purchases-create',
                    "description" => 'Crear compras',
                    "group"       => 'Compras'
                ],
                [
                    "name"        => 'purchases-list',
                    "description" => 'Listar compras',
                    "group"       => 'Compras'
                ],
                [
                    "name"        => 'purchases-show',
                    "description" => 'Ver compras',
                    "group"       => 'Compras'
                ],
                [
                    "name"        => 'purchases-update',
                    "description" => 'Actualizar compras',
                    "group"       => 'Compras'
                ]
            ];
            $role        = Role::where('name', 'admin')->first();
            foreach ($permisions as  $value) {
                $permission = Permission::firstOrCreate($value);
                $role->givePermissionTo($permission);
            }
            $this->info('Permisos de compras creados con exito!');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }

    }
}
