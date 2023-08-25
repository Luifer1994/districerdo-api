<?php

namespace App\Console\Commands\Permissions\Cities;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CityPermissionsManyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-permission-cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Createa all permissions for cities module';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->line('Creando permisos de ciudades...');
        try {
            $permisions = [
                [
                    'name'        => 'cities-module',
                    'description' => 'Módulo de ciudades',
                    'group'       => 'Ciudades'
                ],
                [
                    "name"        => 'cities-create',
                    "description" => 'Crear ciudades',
                    "group"       => 'Ciudades'
                ],
                [
                    "name"        => 'cities-list',
                    "description" => 'Listar ciudades',
                    "group"       => 'Ciudades'
                ],
                [
                    "name"        => 'cities-show',
                    "description" => 'Ver ciudades',
                    "group"       => 'Ciudades'
                ],
                [
                    "name"        => 'cities-update',
                    "description" => 'Actualizar ciudades',
                    "group"       => 'Ciudades'
                ]
            ];
            $role        = Role::where('name', 'admin')->first();
            foreach ($permisions as  $value) {
                $permission = Permission::firstOrCreate($value);
                $role->givePermissionTo($permission);
            }
            $this->info('Permisos de ciudades creados correctamente');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }

    }
}
