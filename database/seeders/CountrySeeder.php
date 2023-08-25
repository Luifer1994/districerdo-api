<?php

namespace Database\Seeders;

use App\Http\Modules\Countries\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::create(
            [
                'name' => 'Colombia',
                'iso_code' => 'CO',
                'iso_code3' => 'COL'
            ]
        );
    }
}
