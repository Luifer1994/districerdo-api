<?php

namespace App\Console\Commands\Permissions\Invoices;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InvoicePermissionsManyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-permission-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Createa all permissions for invoices module';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->line('Creando permisos de facturas...');
        try {
            $permisions = [
                [
                    'name'        => 'invoices-module',
                    'description' => 'Módulo de facturas',
                    'group'       => 'Facturas'
                ],
                [
                    "name"        => 'invoices-create',
                    "description" => 'Crear facturas',
                    "group"       => 'Facturas'
                ],
                [
                    "name"        => 'invoices-list',
                    "description" => 'Listar facturas',
                    "group"       => 'Facturas'
                ],
                [
                    "name"        => 'invoices-show',
                    "description" => 'Ver facturas',
                    "group"       => 'Facturas'
                ],
                [
                    "name"        => 'invoices-update',
                    "description" => 'Actualizar facturas',
                    "group"       => 'Facturas'
                ]
            ];
            $role        = Role::where('name', 'admin')->first();
            foreach ($permisions as  $value) {
                $permission = Permission::firstOrCreate($value);
                $role->givePermissionTo($permission);
            }
            $this->info('Permisos de Facturas creados correctamente');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }

    }
}
