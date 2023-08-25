<?php

namespace App\Console\Commands\Permissions\Dashboard;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardPermissionsManyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-permission-dashboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Createa all permissions for dashboard module';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->line('Creando permisos de panel de control...');
        try {
            $permisions = [
                [
                    'name'        => 'home',
                    'description' => 'Panel de control',
                    'group'       => 'Panel de control',
                ]
            ];
            $role        = Role::where('name', 'admin')->first();
            foreach ($permisions as  $value) {
                $permission = Permission::firstOrCreate($value);
                $role->givePermissionTo($permission);
            }
            $this->info('Permisos de panel de control creados correctamente');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }

    }
}
