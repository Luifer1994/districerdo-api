<?php

namespace App\Http\Modules\Products\Services;

use App\Http\Modules\Categories\Repositories\CategoryRepository;
use App\Http\Modules\Products\Models\Product;
use App\Http\Modules\Products\Repositories\ProductRepository;
use App\Http\Modules\Products\Requests\CreateOrUpdateProductRequest;

class ProductService
{
    protected $productRepository;
    protected $categoryRepository;

    public function __construct(ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Create new Product.
     *
     * @param  array $data
     * @return ?object
     */
    public function createProduct(CreateOrUpdateProductRequest $request): ?object
    {

        $category = $this->categoryRepository->find($request->category_id);
        $request->merge(
            [
                'user_id' => auth()->user()->id,
                'sku' => $this->generateSku($request->name, $category->name)
            ]
        );
        return $this->productRepository->save(new Product($request->all()));
    }

    public function generateSku($product, $category): string
    {
        $sku = substr($product, 0, 1) . substr($category, 0, 1) . rand(100, 999);
        if ($this->productRepository->findBySku($sku)) {
            return $this->generateSku($product, $category); // Return the result of the recursive call
        } else {
            return $sku;
        }
    }
}
