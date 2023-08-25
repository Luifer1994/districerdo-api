<?php

namespace Database\Seeders;

use App\Http\Modules\Users\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role =  Role::create(['name' => 'admin', 'description'  => 'Administrador']);
        $users = [
            [
                'name' => 'Luis',
                'last_name' => 'Almendrales',
                'document_type_id' => 1,
                'document' => '10042880446',
                'email' => 'almendralesluifer@gmail.com',
                'password' => bcrypt('12345678'),
            ]
        ];

        foreach ($users as $value) {
            $user = User::create($value);
            $user->assignRole($role);
        }
    }
}
