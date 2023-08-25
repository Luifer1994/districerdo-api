<?php

namespace App\Console\Commands\Permissions;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreatePermissionsManyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Createa all permissions';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() : void
    {
        try {
            $this->call('create-permission-users');
            $this->call('create-permission-dashboard');
            $this->call('create-permission-roles-and-permissions');
            $this->call('create-permission-clients');
            $this->call('create-permission-cities');
            $this->call('create-permission-document-types');
            $this->call('create-permission-products');
            $this->call('create-permission-invoices');
            $this->call('create-permission-providers');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }
}
