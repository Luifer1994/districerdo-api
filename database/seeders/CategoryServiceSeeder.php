<?php

namespace Database\Seeders;

use App\Http\Modules\Products\Services\ProductService;
use App\Http\Modules\Services\Models\CategoryService;
use App\Http\Modules\Services\Models\Service;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryServiceSeeder extends Seeder
{

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Cerdo',
                'products' => [
                    'PIERNA',
                    'TOCINO',
                    'BRAZUELO',
                    'LOMO',
                    'LOMO SEMI',
                    'CHULETA',
                    'CODILLO',
                    'COSTILLA',
                    'ESPINAZO',
                    'TOCINO ESPECIAL',
                    'OSOBUCO',
                    'OTROS'
                ]
                ],
            [
                'name' => 'Res',
                'products' => []
            ],
            [
                'name' => 'Pollo',
                'products' => []
            ],
            [
                'name' => 'Pescado',
                'products' => []
            ],
            [
                'name' => 'Embutidos',
                'products' => []
            ],
        ];

        foreach ($categories as $categoryInfo) {
            $newCategory = Category::firstOrCreate([
                'name' => $categoryInfo['name'],
                'user_id' => 1,
            ]);

            foreach ($categoryInfo['products'] as $product) {
                $sku = $this->productService->generateSku($product, $categoryInfo['name']);
                Product::firstOrCreate([
                    'name' => $product,
                    'category_id' => $newCategory->id,
                    'sku' => $sku,
                    'user_id' => 1
                ]);
            }
        }
    }


}
