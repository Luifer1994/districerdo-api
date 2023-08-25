<?php

namespace App\Console\Commands\Permissions\DocumentTypes;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DocumentTypePermissionsManyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-permission-document-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Createa all permissions for document types module';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->line('Creando permisos de tipos de documentos...');
        try {
            $permisions = [
                [
                    'name'        => 'document-types-module',
                    'description' => 'Módulo de tipos de documentos',
                    'group'       => 'Tipos de documentos'
                ],
                [
                    "name"        => 'document-types-create',
                    "description" => 'Crear tipos de documentos',
                    "group"       => 'Tipos de documentos'
                ],
                [
                    "name"        => 'document-types-list',
                    "description" => 'Listar tipos de documentos',
                    "group"       => 'Tipos de documentos'
                ],
                [
                    "name"        => 'document-types-show',
                    "description" => 'Ver tipos de documentos',
                    "group"       => 'Tipos de documentos'
                ],
                [
                    "name"        => 'document-types-update',
                    "description" => 'Actualizar tipos de documentos',
                    "group"       => 'Tipos de documentos'
                ]
            ];
            $role        = Role::where('name', 'admin')->first();
            foreach ($permisions as  $value) {
                $permission = Permission::firstOrCreate($value);
                $role->givePermissionTo($permission);
            }
            $this->info('Permisos de tipos de documentos creados con exito!');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }

    }
}
