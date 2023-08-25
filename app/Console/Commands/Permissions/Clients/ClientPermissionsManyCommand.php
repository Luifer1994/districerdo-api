<?php

namespace App\Console\Commands\Permissions\Clients;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ClientPermissionsManyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-permission-clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Createa all permissions for clients module';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->line('Creando permisos de clientes...');
        try {
            $permisions = [
                [
                    'name'        => 'clients-module',
                    'description' => 'Módulo de clientes',
                    'group'       => 'Clientes'
                ],
                [
                    "name"        => 'clients-create',
                    "description" => 'Crear clientes',
                    "group"       => 'Clientes'
                ],
                [
                    "name"        => 'clients-list',
                    "description" => 'Listar clientes',
                    "group"       => 'Clientes'
                ],
                [
                    "name"        => 'clients-show',
                    "description" => 'Ver clientes',
                    "group"       => 'Clientes'
                ],
                [
                    "name"        => 'clients-update',
                    "description" => 'Actualizar clientes',
                    "group"       => 'Clientes'
                ]
            ];
            $role        = Role::where('name', 'admin')->first();
            foreach ($permisions as  $value) {
                $permission = Permission::firstOrCreate($value);
                $role->givePermissionTo($permission);
            }
            $this->info('Permisos de clientes creados correctamente');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }

    }
}
