<?php

namespace Database\Seeders;

use App\Http\Modules\DocumentTypes\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DocumentType::create(
            [
                'code' => 'CC', 'name' => 'Cédula de ciudadanía'
            ]
        );

        DocumentType::create(
            [
                'code' => 'NIT', 'name' => 'Número de identificación tributaria'
            ]
        );
        DocumentType::create(
            [
                'code' => 'CE', 'name' => 'Cédula de extranjería'
            ]
        );
        DocumentType::create(
            [
                'code' => 'TI', 'name' => 'Tarjeta de identidad'
            ]
        );
        DocumentType::create(
            [
                'code' => 'RC', 'name' => 'Registro civil'
            ]
        );
        DocumentType::create(
            [
                'code' => 'PA', 'name' => 'Pasaporte'
            ]
        );
        DocumentType::create(
            [
                'code' => 'PE', 'name' => 'Permiso especial de permanencia'
            ]
        );
    }
}
